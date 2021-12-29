# Magma Glass?
Yep! It's a looking glass for your [lava rocks](https://en.wikipedia.org/wiki/Obsidian). Designed to be a selfhosted alternative to Obsidian Publish, with some extra smarts to keep things interesting.

## So what will it do?
- [ ] Parse Obsidian's markdown files (directly, no databases or middlemen)
  - [x] Heavy use of cache if available
  - [x] Support relative wikilinks
  - [x] Support non-relative wikilinks
  - [ ] Support [Obsidian Aliases](https://help.obsidian.md/How+to/Add+aliases+to+note)
  - [x] Fully support [Obsidian's embedded image format](https://help.obsidian.md/How+to/Embed+files#Developer+notes)
    - [x] Including resizing
  - [ ] Safely support `iframe` embeds
  - [ ] [Comments](https://help.obsidian.md/How+to/Format+your+notes#Comments)
  - [x] Allow marking certain folders as private
  - [x] Parsing/syntax highlighting of code blocks?
  - [x] Mermaid.js
  - [x] Mathjax
- [x] Have a traversible menu structure
- [x] Search
  - [x] Needs to be quick and responsive
- [x] Update your Obsidian from a webhook
  - [x] Github webhook-on-commit -> Notifies Magma Glass -> Pulls your repo and updates it.
  - [ ] Safely, with rollback
- [x] Need no database

### Wishlist
- [ ] Have Obsidian's fancy graph visualization (Yeah, right. Maybe one day when I learn graph theory.)
- ~~[ ] Minor edits, maybe?~~ I have decided against this. No real good reason for ... well, _any_ edits to come from Magma Glass. Instead, just put your Obsidian vault in a Github repo, configure Magma Glass, and push.

## Who is it for?
Me. Mostly. 
