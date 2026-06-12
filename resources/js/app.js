document.addEventListener('alpine:init', () => {
    window.Alpine.data('adminRichText', (wire, prop) => ({
        init() {
            this.$refs.editor.innerHTML = wire.get(prop) ?? '';
        },
        exec(command, value = null) {
            this.$refs.editor.focus();
            document.execCommand(command, false, value);
            this.sync();
        },
        link() {
            const url = window.prompt('Endereço do link (URL):', 'https://');
            if (url) {
                this.exec('createLink', url);
            }
        },
        sync() {
            let html = this.$refs.editor.innerHTML;
            if (html === '<br>' || html === '<div><br></div>') {
                html = '';
            }
            wire.set(prop, html, false);
        },
    }));
});
