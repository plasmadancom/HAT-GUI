# HAT-GUI

Pinout guide &amp; interactive web GUI for MCP23008 and MCP23017 based Raspberry Pi expansion boards. The GUI is fully responsive and adapts to any screen size.

Check-out the [Live Demo.](https://io.plasmadan.com)

## Easy Installer

Our easy installer takes care of the setup process automatically.

```
sudo wget https://git.plasmadan.com/install.sh
sudo sh install.sh
```

This script will automatically enable I2C, install the required packages and setup the Web GUI.

Alternatively, you can install manually. See our [setup guide](#setup-guide).

## Add Your Own Board

Fork this repository and copy any existing board within /src and change it your board name. Your directory will contain a board.php file which details everything about your board. The pinout is setup in php array format, change this to your board's pinout and modify the stylesheet to display your board as you like. See notes within board.php for configuration details. You can use your own version of the easy installer to streamline installation of your custom board onto your Pi, simply edit install.sh and change GITHUB_USERNAME to your own.

```
sudo wget https://github.com/**USERNAME**/PI-GUI/blob/master/install.sh
sudo sh install.sh
```

Equally, you can delete any boards you dont need by simply deleting the directory.

## Contributing

If HAT-GUI is usefull to you please help improve it by contributing. If you have a board you want to pull to the main branch submit a pull request for approval.

We have a few ideas for improvments which we're looking to implement in the future:

* Ditch WiringPi (deprecated)
* Convert to Node.js
    Would be awesome once done, but requires completely re-building from scratch. (╥_╥)

### Image Overlays

Image overlays can be used to add interactive elements to your board such as relays or LEDs.
CSS classes that contain "led" are dynamically displayed according to gpio status.

Example, if you have 3 GPIOs with classes led1, led2, led3 respectively. You can add an image overlay to use for those LEDs.

```
.overlay .dynamic-led {
    width: 30px;
    height: 60px;
    background: url("img/led.png");
}

.overlay .led1 {
    top: 200px;
}

.overlay .led2 {
    top: 300px;
}

.overlay .led3 {
    top: 400px;
}

```

You can also add an overlay to the entire board to add detail.

```
.board:before {
    background-image: url("img/overlay.png");
}
```

# Setup Guide

## Prerequisites

Raspberry Pi with Raspberry Pi OS:
https://www.raspberrypi.org/downloads/raspberry-pi-os/

I recommend a clean Raspberry Pi OS install before proceeding.

Tip: For headless setup, SSH can be enabled by placing a file named 'ssh' (no extension) onto the boot partition of the SD card.

## Enable I2C

I2C must be enabled in raspi-config to allow I2C based HATs to communcate with Raspberry Pi.

```
sudo raspi-config
```

Select 5 Interfacing Options, then P5 I2C. A prompt will appear asking Would you like the I2C interface to be enabled?, select Yes, exit the utility and reboot your Raspberry Pi.

```
sudo reboot
```

Update your Raspberry Pi to ensure all the latest packages are installed.

```
sudo apt update
sudo apt upgrade
```

Install I2C-Tools

```
sudo apt install i2c-tools -y
```

Enable i2c_vc so your Raspberry Pi can detect and read the EEPROM.

```
sudo sh -c "echo 'dtparam=i2c_vc=on' >> /boot/config.txt"
```

For recent versions of the Raspberry Pi (3.18 kernel or later) you will need to add `dtparam=i2c1=on` to the end of `/boot/config.txt`.

```
sudo sh -c "echo 'dtparam=i2c1=on' >> /boot/config.txt"
```

You can increase the I2C bus speed by adding the i2c_baudrate paramter to `/boot/config.txt`.

```
sudo sh -c "echo 'dtparam=i2c_baudrate=400000' >> /boot/config.txt"
```

Add the 'pi' user to the I2C group to avoid having to run the I2C tools as root. This sis usally default anyway.

```
sudo adduser pi i2c
```

Reboot your Raspberry Pi.

```
sudo reboot
```

Test if your HAT is detectable.

```
sudo i2cdetect -y 1
```

You should see a grid of all populated I2C devices.

<p align="center">
    <img alt="I2cdetect output" src="/img/i2cdetect.gif">
</p>

## Install WiringPi

```
sudo apt install wiringpi -y
```

If you wish to write your own scripts using Python, you will need to install WiringPi for Python also.

```
sudo apt install python-pip -y
```

Install WiringPi for Python.

```
sudo pip install wiringpi
```

## Install Apache & PHP

```
sudo apt install apache2 php libapache2-mod-php -y
```

Test the webserver is working. Navigate to http://localhost/ on the Pi itself, or http://192.168.1.10 (whatever the Pi's IP address is) from another computer on the network. Use the snippet below to get the Pi's IP address in command line.

```
hostname -I
```

## Install HAT-GUI

You need to clone the web GUI files from the 'gui' subfolder on GitHub, to do that we need to install subversion.

*Note: The html files are for the live demo, you don't need to install them.*

```
sudo apt install subversion -y
```

Navigate to the web root.

```
cd /var/www/html
```

Empty default Apache files

```
sudo rm -rf *
```

Clone web GUI files (you must include the period at the end).

```
sudo svn checkout https://github.com/plasmadancom/HAT-GUI/trunk/gui .
```

Be sure to set file permissions to 755 in the web directory.

```
sudo chmod -R 755 /var/www
```

That's it! reload the web page to see the HAT-GUI web page.

## Optional: Install vsftpd for Easier File Editing

```
sudo apt install vsftpd -y
```

Change user for vsftpd.

```
sudo chown -R pi /var/www
```

Edit vsftpd.conf.

```
sudo nano /etc/vsftpd.conf
```

Uncomment the following line:

```
write_enable=YES
```

Add the following line:

```
force_dot_files=YES
```

Save and exit nano, then restart vsftpd.

```
sudo service vsftpd restart
```

You should now be able to login via FTP.

## License

MIT © Dan Jones - [PlasmaDan.com](https://plasmadan.com)