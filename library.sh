#!/bin/bash

################################################################################
# Time and String Manipulation Handlers
################################################################################

################################################################################
# Converts seconds to a nicely formatted string.
secondsToHumanTime() {
  total="$1"
  if [[ -z "$total" ]]; then
    # empty
    text="indefinitely."
  else
    # We now have the amount of time left.
    # left=`expr $total - $s`

    # Let's reset some variables.
    h=0; m=0; s=0;
    ((hours=$total/3600))
    ((minutes=($total%3600)/60))
    ((seconds=$total%60))

    # Now we're going to make the remaining time look pretty.
    if [[ $hours -gt 0 ]]; then
      if [[ $hours -gt 1 ]]; then
        string="$hours hours"
      else
        string="$hours hour"
      fi
      # Some nice formatting glue
      if [[ $minutes -gt 0 ]]; then
        if [[ $seconds -gt 0 ]]; then
          string="$string, "
        else
          string="$string and "
        fi
      else
        string="$string."
      fi
    fi

    if [[ $minutes -gt 0 ]]; then
      if [[ $minutes -gt 1 ]]; then
        string="$string $minutes minutes"
      else
        string="$string $minutes minute"
      fi
      if [[ $hours -gt 0 ]]; then
        if [[ $seconds -gt 0 ]]; then
          string="$string, and "
        else
          string="$string."
        fi
      elif [[ $seconds -gt 0 ]]; then
        string="$string and "
      else
        string="$string."
      fi
    fi

    if [[ $seconds -gt 0 ]]; then
      if [[ $seconds -gt 1 ]]; then
        string="$string $seconds seconds."
      else
        string="$string $seconds second."
      fi
    fi

    # Cleanup the string. This shouldn't be necessary anymore, but, whatever.
    string=`echo $string | sed 's/ ,/, /g' | sed 's/  */ /g'`
    echo $string
  fi
}

################################################################################
# Parses non-standard arguments
parseTime() {
  arg=$1
  arg=`echo "${arg}"|sed 's/^ *//g'|sed 's/ *$//g'`

  args=(${arg// / })
  count=${#args[*]}

  if [[ $count -eq 1 ]]; then
    echo `parseTimeArg $arg`
    exit
  elif [[ $count -eq 2 ]]; then
    t1=`parseTimeArg ${args[0]}h`
    t2=`parseTimeArg ${args[1]}m`
    (( time=$t1 + $t2 ))
    echo $time
    exit
 else
   # there are more than three arguments, so just make this indefinite
   echo "0"
   exit
 fi
}

################################################################################
# Subhandler to process times with just numbers or m/h afterward
parseTimeArg() {
  arg=$1
  if [[ $arg =~ ([0-9]{1,})$ ]]; then
    (( arg=$arg*60 ))
    echo $arg
    exit
  else
    [[ $arg =~ ([0-9]{1,})([hHmM]{1,}) ]]
    time=${BASH_REMATCH[1]}; unit=${BASH_REMATCH[2]}
    if [[ $unit =~ ^([hH]{1,}) ]]; then
      (( arg=$time*60*60))
      echo $arg # return the hours in seconds
      exit
    elif [[ $unit =~ ^([mM]{1,}) ]]; then
      (( arg=$time*60 ))
      echo $arg #return the minutes in seconds
      exit
    else
      # default to minutes... we shouldn't get here
      (( arg=$time*60 ))
      echo $arg #return the minutes in seconds
      exit
    fi
  fi
}

################################################################################
# Standard library below
################################################################################

if [[ -z $alfred_workflow_data ]]; then
  if [[ -e '/Applications/Alfred 3.app' ]]; then
    alfred_workflow_data="${HOME}/Library/Application Support/Alfred 3/Workflow Data/"
    alfred_workflow_cache="${HOME}/Library/Caches/com.runningwithcrayons.Alfred-3/Workflow Data/"
  elif [[ -e "${HOME}/Applications/Alfred 3.app" ]]; then
    alfred_workflow_data="${HOME}/Library/Application Support/Alfred 3/Workflow Data/"
    alfred_workflow_cache="${HOME}/Library/Caches/com.runningwithcrayons.Alfred-3/Workflow Data/"
  elif [[ -e '/Applications/Alfred 2.app' ]]; then
    alfred_workflow_data="${HOME}/Library/Application Support/Alfred 2/Workflow Data/"
    alfred_workflow_cache="${HOME}/Library/Caches/com.runningwithcrayons.Alfred-3/Workflow Data/"
  elif [[ -e "${HOME}/Applications/Alfred 2.app" ]]; then
    alfred_workflow_data="${HOME}/Library/Application Support/Alfred 2/Workflow Data/"
    alfred_workflow_cache="${HOME}/Library/Caches/com.runningwithcrayons.Alfred-3/Workflow Data/"
  else
    (>&2 echo 'ERROR: Cannot find a copy of Alfred.')
    exit 1
  fi
fi

if [[ ! -e "${alfred_workflow_data}" ]]; then
  mkdir -p "${alfred_workflow_data}"
fi
if [[ ! -e "${alfred_workflow_cache}" ]]; then
  mkdir -p "${alfred_workflow_cache}"
fi

VPREFS="${alfred_workflow_data}/"
NVPREFS="${alfred_workflow_cache}/"

RESULTS=()

################################################################################
# Adds a result to the result array
#
# $1 uid
# $2 arg
# $3 title
# $4 subtitle
# $5 icon
# $6 valid
# $7 autocomplete
###############################################################################
addResult() {
  RESULT="<item uid='$(xmlEncode "$1")' arg='$(xmlEncode "$2")' valid='$6' autocomplete='$7'><title>$(xmlEncode "$3")</title><subtitle>$(xmlEncode "$4")</subtitle><icon>$(xmlEncode "$5")</icon></item>"
  RESULTS+=("$RESULT")
}

###############################################################################
# Prints the feedback xml to stdout
###############################################################################
getXMLResults() {
  echo "<?xml version='1.0'?><items>"

  for R in ${RESULTS[*]}; do
    echo "$R" | tr "\n" " "
  done

  echo "</items>"
}

###############################################################################
# Escapes XML special characters with their entities
###############################################################################
xmlEncode() {
  echo "$1" | sed -e 's/&/\&amp;/g' -e 's/>/\&gt;/g' -e 's/</\&lt;/g' -e "s/'/\&apos;/g" -e 's/"/\&quot;/g'
}

###############################################################################
# Read the bundleid from the workflow's info.plist
###############################################################################
getBundleId() {
  /usr/libexec/PlistBuddy  -c "Print :bundleid" "info.plist"
}

###############################################################################
# Get the workflow data dir
###############################################################################
getDataDir() {
  local BUNDLEID=$(getBundleId)
  echo "$alfred_workflow_data"
}

### Convert etime (from `ps`) to seconds. I love stackoverflow.
#https://stackoverflow.com/questions/14652445/parse-ps-etime-output-and-convert-it-into-seconds
function etimeToSeconds() {
	echo $1 | awk -F $':' -f <(cat - <<-'EOF'
  {
    if (NF == 2) {
      print $1*60 + $2
    } else if (NF == 3) {
      split($1, a, "-");
      if (a[2] > 0) {
        print ((a[1]*24+a[2])*60 + $2) * 60 + $3;
      } else {
        print ($1*60 + $2) * 60 + $3;
      }
    }
  }
EOF
)
}