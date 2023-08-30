// On récupère tous les toggle HTML
const switchs = document.querySelectorAll('input[data-actif-id]');

// On parcour le tableau des switchs et pour chaque élément on écoute le click
switchs.forEach((element) => {
    element.addEventListener('click', (event) => {
        // const articleId = event.target.getAttribute('data-actif-id');
        // OU
        const articleId = event.target.dataset.actifId;
        switchVisibility(articleId);
    });
});

async function switchVisibility(id) {
    const response = await fetch(`/admin/article/switch/${id}`);

    if (response.status < 200 || response.status > 299) {
        console.error(response);
    }
}