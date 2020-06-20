#!/usr/bin/python

# mcp23017_chaser.py - Infinite chaser sequence for multiple MCP23017 expanders
# 
# Copyright (C) 2020 Dan Jones - https://plasmadan.com
# 
# -----------------------------------------------------------------------------
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
# 
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
# -----------------------------------------------------------------------------


# Import dependencies
import wiringpi
from time import sleep


# Setup
pinBase = 100                                                # WiringPi pinBase (anything above 64)
devices = [0x20, 0x21, 0x22, 0x23, 0x24, 0x25, 0x26, 0x27]   # I2C device list
outputs = [0, 1, 2, 3]                                       # WiringPi output port list

# Timing
hold_on = 0.02                                               # Time to hold outputs on (seconds)
hold_off = 0.02                                              # Delay between each output on (seconds)


wiringpi.wiringPiSetup()                                     # Initialise WiringPi

sequence = []                                                # Create the sequence before running
count = pinBase

for i2c in devices + list(reversed(devices)):                # Loop devices normally, then reversed
    wiringpi.mcp23017Setup(count, i2c)                       # Set up the pins and i2c address
    
    if count > len(devices)*100:                             # Reverse direction
        for i in list(reversed(outputs)):
            if i == 0 and count == len(devices)*2*100:       # Skip first and last to avoid repeats
                continue
            
            elif i == len(outputs)-1 and count == (len(devices)+1)*100:
                continue
            
            sequence.append(count + i)                       # Add to sequence
        
        count += 100
        
        continue
    
    for i in outputs:
        wiringpi.pinMode(count + i, 1)                       # Set output mode
        sequence.append(count + i)                           # Add to sequence
    
    count += 100

print 'MCP23017 Infinite Chaser Sequence Loaded!'

try:
    while True:                                              # Loop forever
        for i in sequence:
            wiringpi.digitalWrite(i, 1)                      # On
            sleep(hold_on)
            wiringpi.digitalWrite(i, 0)                      # Off
            sleep(hold_off)

except Exception as e:                                       # Something went wrong
   print e

finally:
        for i in sequence:
            wiringpi.digitalWrite(i, 0)                      # Reset