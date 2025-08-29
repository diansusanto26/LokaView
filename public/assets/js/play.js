const lockedEpisodeElement = document.getElementById('locked-episode-element');
const unlockEpisodeButton = document.getElementById('unlock-episode-button');
const openEpisodeElement = document.getElementById('open-episode-element');
const listEpisodeButton = document.getElementById('list-episode-button');
const listEpisodeElement = document.getElementById('list-episode-element');
const closeListEpisodeButton = document.getElementById('close-list-episode-button');

if (unlockEpisodeButton) {
    unlockEpisodeButton.addEventListener('click', () => {
        openEpisodeElement.classList.remove('hidden');
    });
}

if (listEpisodeButton) {
    listEpisodeButton.addEventListener('click', () => {
        listEpisodeElement.classList.toggle('hidden');
    });
}

if (closeListEpisodeButton) {
    closeListEpisodeButton.addEventListener('click', () => {
        listEpisodeElement.classList.add('hidden');
    });
}
