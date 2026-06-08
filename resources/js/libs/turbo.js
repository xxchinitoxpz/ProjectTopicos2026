import * as Turbo from '@hotwired/turbo';

// Custom Turbo Stream Action for redirecting the top-level window
Turbo.StreamActions.redirect = function() {
    const url = this.getAttribute('url');
    if (url) {
        Turbo.visit(url);
    }
};

export default Turbo;
