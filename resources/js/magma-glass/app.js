export default() => ({
    'sidebar': false,
    'loaded': false,
    'theme': localStorage.theme,
    'searchTerm': '',
    'article': {
        title: 'Loading',
        content: 'Loading...'
    },
    'articleUpdated': false,
    'searchResults': [],
    'showSearchResults': false,

    init() {
        if(!location.pathname.startsWith('/a')) {
            this.loaded = true;
            return;
        }

        let storageID = 'article_content_cache.' + decodeURI(location.pathname.substring(2));

        let storedArticle = this.getWithExpiry(storageID);
        if(storedArticle !== null) {
            this.article = {
                title: storedArticle.title,
                content: storedArticle.content,
            }
            this.loaded = true;
        }
    },

    postInit() {
        let currentItem;
        let currentKey;

        for (let i = 0; i < localStorage.length; i++){
            currentItem = localStorage.getItem(localStorage.key(i));
            currentKey = localStorage.key(i);

            if (currentItem && currentItem.includes("expiry")) {
                this.getWithExpiry(currentKey);
            }
        }

        if(!this.loaded) {
            this.updateArticle(decodeURI(location.pathname.substring(2)))
            this.loaded = true;
        }

        this.postRender();
    },

    updateArticle(path, back = false) {
        if(!location.pathname.startsWith('/a/')) {
            self.location = '/a/' + path;
            return;
        }

        if(path.endsWith('.md')) {
            path = path.slice(0, path.length - 3);
        }
        if(!path.startsWith('/')) {
            path = '/' + path;
        }

        let storedArticle = this.getWithExpiry('article_content_cache.' + path);

        if (storedArticle !== null) {
            this.article = storedArticle;
        } else {
            axios.get('/get-article/', {
                params: {
                    articlePath: path
                }
            }).then(response => {
                if (response.status !== 200) {
                    console.error(`Article fetch failed: ${response.status} - ${response.statusText}`);
                    return;
                }

                return response.data;
            }).then(data => {
                if (!data) return;

                this.article = {
                    title: data.title,
                    content: data.content
                }

                this.$nextTick(() => this.postRender());

                this.setWithExpiry('article_content_cache.' + decodeURI(location.pathname.substring(2)), this.article, 300000);
            }).catch(error => {
                console.error('Failed to fetch article:', error);
            });
        }


        if(!back && location.origin + '/a' + path !== window.location.href) {
            history.pushState(null, document.title, location.origin + '/a' + path);
        }

        this.sidebar = false;
        this.$nextTick(() => this.postRender());
    },

    postRender() {
        hljs.highlightAll();
        mermaid.init(undefined, "code.language-mermaid");
        MathJax.typeset()
    },

    fetchSearchResults($event) {
        axios.get('/search/', {
            params: {
                searchTerm: this.searchTerm
            }
        }).then(response => {
            if (response.status !== 200) {
                console.error(`Search failed: ${response.status} - ${response.statusText}`);
                return;
            }

            return response.data;
        }).then(data => {
            if (!data) return;
            this.searchResults = data;
        }).catch(error => {
            console.error('Search request failed:', error);
        });
    },

    setWithExpiry(key, value, ttl) {
        const item = {
            value: value,
            expiry: new Date().getTime() + ttl,
        }

        localStorage.setItem(key, JSON.stringify(item))
    },

    getWithExpiry(key) {
        const itemStr = localStorage.getItem(key)

        if (!itemStr) {
            return null
        }

        try {
            const item = JSON.parse(itemStr)

            if (!item || !item.expiry) {
                return null;
            }

            if (new Date().getTime() > item.expiry) {
                localStorage.removeItem(key)
                return null
            }
            return item.value
        } catch (e) {
            localStorage.removeItem(key);
            return null;
        }
    }
})
