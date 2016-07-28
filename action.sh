#!/bin/bash
################################################################################
# Control caffeinate script
################################################################################

. library.sh

data="${alfred_workflow_data}"
pref="${data}/preferences"

if [ ! -f "$pref" ]; then
  echo "i" > "$pref"
fi
pref=`cat "${pref}"`
# echo $pref

if [[ -z $1 ]]; then
  # destroy any running instance of caffeinate, quietly
  killall caffeinate 2>/dev/null
  # activate caffeinate with preferences and no time
  caffeinate -$pref > /dev/null 2>&1 &
  # disown the last process ... not sure if this is necessary
  disown %%
  # send a message to the fans at home
  echo "Your computer will not sleep until you allow it to do so."
  # we're done here
  exit
fi

if [[ $1 = 'disable' ]]; then
  killall caffeinate 2>/dev/null
  echo "Your computer can now go to sleep."
  exit
elif [[ $1 = 'enable' ]]; then
  # destroy any running instance of caffeinate, quietly
  killall caffeinate 2>/dev/null
  # activate caffeinate with preferences and no time
  caffeinate -$pref > /dev/null 2>&1 &
  # disown the last process ... not sure if this is necessary
  disown %%
  # send a message to the fans at home
  echo "Your computer will not sleep until you allow it to do so."
  # we're done here
  exit
elif [[ $1 =~ ([0-9]{1,}) ]]; then
  # destroy any running instance of caffeinate, quietly
  killall caffeinate 2>/dev/null
  # enable caffeinate for the specified time (in seconds)
  caffeinate -t $1 -$pref >/dev/null 2>&1 &
  # disown the last process ... not sure if this is necessary
  disown %%
  # Create the message.
  string=`secondsToHumanTime "$1"`
  echo "Caffeinate will now be active for $string"
elif [[ $1 = 'help' ]]; then
  osascript applescripts/conf-and-help.scpt $1 > /dev/null 2>&1 &
  exit
elif [[ $1 = 'configure' ]]; then
  osascript applescripts/conf-and-help.scpt $1 > /dev/null 2>&1 &
  exit
fi
