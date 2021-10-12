export default() => ({
    'sidebar': false,
    'loaded': false,
    'theme': localStorage.theme,
    'searchTerm': '',
    'article': {
        title: 'Loading',
        content: 'Loading...'
    },
    'searchResults': [],
    'showSearchResults': false,

    init() {
        console.log('Initing');
        if(!location.pathname.startsWith('/a/')) {
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
            console.log("loaded from cache!");
        }

        console.log("Stored article was ");
        console.log(storedArticle);
    },

    postInit() {
        let currentItem;
        let currentKey;

        for (let i = 0; i < localStorage.length; i++){
            currentItem = localStorage.getItem(localStorage.key(i));
            currentKey = localStorage.key(i);

            if (currentItem.includes("expiry")) {
                this.getWithExpiry(currentKey);
            }
        }

        if(!this.loaded) {
            console.log("We didn't load from cache, fetch it.");
            this.updateArticle(decodeURI(location.pathname.substr(2)))
            this.loaded = true;
        }
    },

    updateArticle(path, back = false) {
        console.log("Asked to update article to " + path);

        if(!location.pathname.startsWith('/a/')) {
            self.location = '/a/' + path;
        }

        if(path.endsWith('.md')) {
            path = path.substr(0, path.length - 3);
        }
        if(!path.startsWith('/')) {
            path = '/' + path;
        }

        let storedArticle = this.getWithExpiry('article_content_cache.' + path);

        if (storedArticle !== null) {
            console.log("Got an article!");
            this.article = storedArticle;
        } else {
            axios.get('/get-article/', {
                params: {
                    articlePath: path
                }
            }).then(response => {
                if (!response.status === 200) alert(`Something went wrong: ${response.status} - ${response.statusText}`);

                return response.data;
            }).then(data => {
                this.article = {
                    title: data.title,
                    content: data.content
                }

                this.setWithExpiry('article_content_cache.' + decodeURI(location.pathname.substr(2)), this.article, 300000);
            });
        }


        if(!back && location.origin + '/a' + path !== window.location.href) {
            history.pushState(null, document.title, location.origin + '/a' + path);
        }

        this.sidebar = false;
    },

    fetchSearchResults($event) {
        axios.get('/search/', {
            params: {
                searchTerm: this.searchTerm
            }
        }).then(response => {
            if (!response.status === 200) alert(`Something went wrong: ${response.status} - ${response.statusText}`);

            return response.data;
        }).then(data => {
            this.searchResults = data;
            console.log(data);
        });
    },

    setWithExpiry(key, value, ttl) {
        const item = {
            value: value,
            expiry: new Date().getTime() + ttl,
        }

        console.log("Storing " + key + " as:");
        console.log(item);
        localStorage.setItem(key, JSON.stringify(item))
    },

    getWithExpiry(key) {
        const itemStr = localStorage.getItem(key)
        console.log("Retrieved " + key + " as:");
        console.log(itemStr);

        if (!itemStr) {
            return null
        }

        const item = JSON.parse(itemStr)

        if (new Date().getTime() > item.expiry) {
            localStorage.removeItem(key)
            return null
        }
        return item.value
    }
})
