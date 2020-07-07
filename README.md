# Zootaxa cites data

Data on what articles Zootaxa cites.



https://twitter.com/egonwillighagen/status/1280068394990080000


## Anystyle

On my Mac:

```
sudo gem install anystyle-cli
ERROR:  Error installing anystyle-cli:
	bibtex-ruby requires Ruby version >= 2.4.0.
```

Hmmm:
```
ruby -v
ruby 2.3.7p456 (2018-03-28 revision 63024) [universal.x86_64-darwin18]
```

So, we need to update (see [Install Ruby on Your Mac: Everything You Need to Get Going](https://stackify.com/install-ruby-on-your-mac-everything-you-need-to-get-going/)

```
brew install ruby
```

Add the new ruby to our path, then load that into the current shell:
```
If you need to have ruby first in your PATH run:
  echo 'export PATH="/usr/local/opt/ruby/bin:$PATH"' >> /Users/rpage/.bash_profile

source ~/.bash_profile
```

Now we can install ```anystyle``` … but it doesn’t work. We need to (see https://stackoverflow.com/a/14138490/9684)

```
export PATH=/usr/local/lib/ruby/gems/2.7.0/bin:$PATH
source ~/.bash_profile
```

Now it works!.





