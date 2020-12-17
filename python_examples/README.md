# Python Examples

We have provided some example Python scripts to get you started. You will need to install WiringPi for Python to use them.

```
sudo apt install python-pip -y
sudo pip install wiringpi
```


## mcp23017_chaser.py

An infinite Knight Rider style chaser sequence which works with multiple MCP23017 expanders. A great tool for testing your boards.

<p align="center">
    <a href="http://www.youtube.com/watch?feature=player_embedded&v=CksWK6oX5S8" target="_blank">
        <img src="/img/mcp23017_chaser.gif" alt="MCP23017 Chaser Demo" width="520" height="293" border="10">
    </a>
</p>

```
sudo wget https://github.com/plasmadancom/HAT-GUI/raw/master/python_examples/mcp23017_chaser.py
sudo python mcp23017_chaser.py
```

## mcp23008_chaser.py

As above, for use with MCP23008 expanders.

```
sudo wget https://github.com/plasmadancom/HAT-GUI/raw/master/python_examples/mcp23008_chaser.py
sudo python mcp23008_chaser.py
```
