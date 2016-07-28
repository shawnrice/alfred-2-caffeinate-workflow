[Caffeinate](https://developer.apple.com/library/mac/documentation/Darwin/Reference/Manpages/man8/caffeinate.8.html) is a native OS X command line utility that solves the problem of your Mac constantly falling asleep on you. This is especially annoying when you're trying to read something, and your screen keeps dimming and then turning off. This workflow allows you to interface with Caffeinate so that you needn't open the terminal and send commands. Caffeinate was introduced in Mountain Lion (10.8), and it basically replicates what the Caffeine utility ([web](http://lightheadsw.com/caffeine/) | [app store](http://itunes.apple.com/us/app/caffeine/id411246225) from [Lighthead Software](http://lightheadsw.com/)) does.

Option Configuration

You can configure how you want Caffeinate to work for you. Do you want to keep the system awake? Just the display? Everything? Just type "caff configure" (or "caff c"), and you can set the options easily. If you want to change how it works later, then just run the config again. Change however frequent you want!

Defaults

We default to "i" or just to keep the system from idling (which lets you display turn off). Just run the config and choose more options to as you please. Multiple options are available by pressing cmd while clicking.

Commands

Just type "caff" to get started. It will tell you the status and give you the most relevant option first.

Example Arguments

"caff e" enables caffeinate indefinitely "caff d" disables caffeinate "caff 15 2" enables caffeinate for 15 hours and 2 minutes "caff 23" enables caffeinate for 23 minutes "caff 2h" enables caffeinate for 2 hours "caff configure" opens the configuration dialog "caff help" opens the help dialog.

Note: v1 and v2 were written in PHP. v3 is now simply a bash script and is much faster.