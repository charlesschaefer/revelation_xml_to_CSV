This is a simple PHP script that converts [Gnome Revelation's](https://revelation.olasagasti.info/) exported XML password file to a CSV file that can be read by [KeePassXC](https://keepassxc.org/).

**Caution**: both the revelation's XML file and the resultant CSV file are unencrypted and anyone with access to them can read your passwords. So as soon as you import your CSV file to KeePassXC database format, you should destroy the XML and CSV files to avoid security exposures.
