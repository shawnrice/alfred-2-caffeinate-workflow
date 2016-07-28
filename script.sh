#!/bin/bash
#
################################################################################
# Script filter for Caffeinate Control
################################################################################

. library.sh
datadir=`getDataDir`

if [ ! -d "$datadir" ]; then
  mkdir "$datadir"
fi

#### Start Caffeinate Script Filter
arg=$1

# first, let's do something if there are no commands.
if [[ -z $arg ]]; then
  cmd=$(ps -eo etime,args|grep caffeinate|grep -v grep|sed -e 's|^[[:space:]]*||')

  if [[ -z "$cmd" ]]; then
    addResult "" "configure" "Configure Caffeinate Control" "Configure how you want your computer to stay awake." "images/configure.png" "yes" "configure"
    addResult "" "help" "Caffeinate Control Help" "Read the help files for Caffeinate Control." "images/blue-question.png" "yes" "help"
    addResult "" "enable" "Enable Caffeinate forever" "Never let your computer sleep." "images/green-coffee.png" "yes" "enable"
    addResult "" "status" "Caffeinate is not running" "Your computer is tired." "images/off.png" "yes" "caf"
  else
    # Just grab how many seconds caffeinate was activated for
    [[ $cmd =~ ([0-9:]*)( )(caffeinate -t )([0-9]*)([a-z -]*) ]]
    total=${BASH_REMATCH[4]}
    # Convert how long it has been running for to seconds
    running=$(etimeToSeconds ${BASH_REMATCH[1]})
    # Take the difference
    total=$((total - running))

    if [[ -z "$total" ]]; then
      text="Currently, your computer will never sleep."
    else
      string=$(secondsToHumanTime $total)
      text="Caffeinate will be active for another $string"
    fi
    addResult "" "help" "Caffeinate Control Help" "Read the help files for Caffeinate Control." "images/blue-question.png" "yes" "help"
    addResult "" "configure" "Configure Caffeinate Control" "Configure how you want your computer to stay awake." "images/configure.png" "yes" "configure"
    addResult "" "disable" "Disable Caffeinate" "Let your computer sleep again." "images/red-coffee.png" "yes" "disable"
    addResult "" "status" "Caffeinate is active" "$text" "images/on.png" "yes" "caf"

    getXMLResults
    exit
  fi

  getXMLResults
  exit
else
  # There is an argument, so let's deal with it
  cmd=`ps -eo etime,args|grep caffeinate|grep -v grep`

  if [[ $arg =~ ^(c|C)([oO]*) ]]; then
    addResult "configure" "configure" "Configure Caffeinate Control" "Configure how you want your computer to stay awake." "images/configure.png" "yes" "configure"
  elif [[ $arg =~ ^(h|H)([eE]*) ]]; then
    addResult "help" "help" "Caffeinate Control Help" "Read the help files for Caffeinate Control" "images/blue-question.png" "yes" "help"
  elif [[ $arg =~ ([0-9]{1,}) ]]; then
    time=`parseTime "$arg"` #convert into seconds
    if [[ $time -gt 86399 ]]; then
      display="a really long time."
      subdisplay=`secondsToHumanTime "$time"`
    else
      display=`secondsToHumanTime "$time"`
      subdisplay="$time seconds."
    fi
    if [[ $time -eq 0 ]]; then
      addResult "" "enable" "Enable Caffeinate forever" "Never let your computer sleep." "images/green-coffee.png" "yes" "enable"
    else
      addResult "" "$time" "Enable Caffeinate for $display" "Enable Caffeinate for $subdisplay" "images/green-coffee.png" "yes" "enable"
    fi
  else
    if [[ -z "$cmd" ]]; then
      addResult "" "enable" "Enable Caffeinate forever" "Never let your computer sleep." "images/green-coffee.png" "yes" "enable"
    else
      addResult "" "disable" "Disable Caffeinate" "Let your computer sleep again." "images/red-coffee.png" "yes" "disable"
    fi
  fi
  getXMLResults
  exit
fi
