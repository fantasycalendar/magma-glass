# Magma Glass?
Yep! It's a looking glass for your [lava rocks](https://en.wikipedia.org/wiki/Obsidian). Designed to be a selfhosted alternative to Obsidian Publish, with some extra smarts to keep things interesting.

## So what will it do?
- [x] Parse Obsidian's markdown files (directly, no databases or middlemen)
  - [x] Heavy use of cache if available
  - [x] Support relative wikilinks
  - [ ] Support non-relative wikilinks
  - [ ] Allow marking certain folders as private
  - [ ] Parsing/syntax highlighting of code blocks?
- [ ] Have a traversible menu structure
- [ ] Search
  - [ ] Needs to be quick and responsive
- [ ] Update your Obsidian from a webhook
  - [ ] Github webhook-on-commit -> Notifies Magma Glass -> Pulls your repo and updates it.
  - [ ] Safely, with rollback
- [x] Need no database

### Wishlist
- [ ] Have Obsidian's fancy graph visualization (Yeah, right. Maybe one day when I learn graph theory.)
- [ ] Minor edits?

## Who is it for?
Me. Mostly. 
