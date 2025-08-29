const params = new URLSearchParams(window.location.search);
const coinPackage = params.get('coin_package');
if (coinPackage) {
    const radio = document.querySelector(`input[type=radio][name=coin_package][value='${coinPackage}']`);
    if (radio) {
        radio.checked = true;
        radio.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // clear url search params
        const url = new URL(window.location.href);
        url.searchParams.delete("coin_package");
        window.history.pushState({}, "", url);
    }
}
