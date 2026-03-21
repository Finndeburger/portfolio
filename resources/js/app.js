import "./bootstrap";

import Alpine from "alpinejs";

// searchApp component

Alpine.data("searchApp", () => ({
    query: "",

    sites: [],

    suggestions: [],

    activeIndex: -1,

    init() {
        fetch("/api/sites")
            .then((res) => res.json())

            .then((data) => {
                this.sites = data;
            });
    },

    matchesSite(site, q) {
        const query = q.toLowerCase();

        return (
            site.title.toLowerCase().includes(query) ||
            site.tags.some((tag) => tag.toLowerCase().includes(query))
        );
    },

    canType(nextChar) {
        const candidate = (this.query + nextChar).toLowerCase();

        if (candidate.length === 0) return true;

        // Allow all typing until sites have loaded from the API
        if (this.sites.length === 0) return true;

        return this.sites.some((site) => this.matchesSite(site, candidate));
    },

    updateSuggestions() {
        this.activeIndex = -1;

        if (!this.query) {
            this.suggestions = [];

            return;
        }

        this.suggestions = this.sites.filter((site) =>
            this.matchesSite(site, this.query),
        );
    },

    handleKeydown(e) {
        if (e.key === "Enter") {
            e.preventDefault();

            let searchQuery = this.query.trim();

            if (
                this.activeIndex >= 0 &&
                this.activeIndex < this.suggestions.length
            ) {
                searchQuery = this.suggestions[this.activeIndex].title;
            }

            if (searchQuery) {
                window.location.href =
                    "/results?q=" + encodeURIComponent(searchQuery);
            }

            return;
        }

        if (e.key === "ArrowDown") {
            e.preventDefault();

            if (this.suggestions.length > 0) {
                this.activeIndex =
                    (this.activeIndex + 1) % this.suggestions.length;
            }

            return;
        }

        if (e.key === "ArrowUp") {
            e.preventDefault();

            if (this.suggestions.length > 0) {
                this.activeIndex =
                    this.activeIndex <= 0
                        ? this.suggestions.length - 1
                        : this.activeIndex - 1;
            }

            return;
        }

        if (e.key.length > 1) return;

        if (!this.canType(e.key)) {
            e.preventDefault();
        }
    },

    selectSuggestion(site) {
        window.location.href = "/results?q=" + encodeURIComponent(site.title);
    },
}));

Alpine.start();
