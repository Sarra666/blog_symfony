// On récupère tous les toggle HTML
const switchs = document.querySelectorAll('input[data-actif-id]');

//On parcourt le tableau des switchs et pour chaque élément on écoute le click
switchs.forEach((element) => {
    element.addEventListener('click', (event) => {
       // const articlId = event.target.getAttribute('data-actif-id');
       //OU
       const articleId = event.target.dataset.actifId;
       switchVisibility(articleId);
    });
});
async function switchVisibility(id) {
    const response = await fetch(`/admin/article/switch/${id}`); // on a mis des back tick pas des simple quote
    // /admin/article/switch/${id} est une URL ; ${id} est un paramètre d'URL
    if(response.status < 200 || response.status > 299){   //numéro de protocole d'erreur http
        console.error(await response);
    }
}