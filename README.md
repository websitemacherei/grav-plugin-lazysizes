[![Build Status](https://travis-ci.org/websitemacherei/grav-plugin-lazysizes.svg?branch=master)](https://travis-ci.org/websitemacherei/grav-plugin-lazysizes)

# Lazysizes Plugin

The **Lazysizes** Plugin provides support for lazyloading images defined in the markdown of a page. Instead of manipulating the rendered content it extends the markdown parser and wont't work with editors that are not using the Grav Parsedown Parser, like tinymce-editor for example.  

## Installation

Installing the Lazysizes plugin can be done in one of three ways: The GPM (Grav Package Manager) installation method lets you quickly install the plugin with a simple terminal command, the manual method lets you do so via a zip file, and the admin method lets you do so via the Admin Plugin.

### GPM Installation (Preferred)

To install the plugin via the [GPM](http://learn.getgrav.org/advanced/grav-gpm), through your system's terminal (also called the command line), navigate to the root of your Grav-installation, and enter:

    bin/gpm install lazysizes

This will install the Lazysizes plugin into your `/user/plugins`-directory within Grav. Its files can be found under `/your/site/grav/user/plugins/lazysizes`.

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `lazysizes`. You can find these files on [GitHub](https://github.com/websitemacherei/grav-plugin-lazysizes) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/lazysizes
	
> NOTE: This plugin is a modular component for Grav which may require other plugins to operate, please see its [blueprints.yaml-file on GitHub](https://github.com/websitemacherei/grav-plugin-lazysizes/blob/master/blueprints.yaml).

### Admin Plugin

If you use the Admin Plugin, you can install the plugin directly by browsing the `Plugins`-menu and clicking on the `Add` button.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/lazysizes/lazysizes.yaml` to `user/config/plugins/lazysizes.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

Note that if you use the Admin Plugin, a file with your configuration named lazysizes.yaml will be saved in the `user/config/plugins/`-folder once the configuration is saved in the Admin.
