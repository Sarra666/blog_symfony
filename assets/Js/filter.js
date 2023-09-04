import { debounce } from "lodash";
import { Flipper, spring} from "flip-toolkit";



/**
 *  Class for filter article in AJAX
 * 
 * @property {HTMLElement} pagination -Element html with pagination nav
 * @property {HTMLElement} content - Element html with the List of article
 * @property {HTMLElement} sortable - Element html with the sortable btn
 * @property {HTMLFormElement} form - Element html with the form filter
 * @property {HTMLElement} count - Element html with thetotal item
 * @property {int} page - the number of the current page 
 * */

export class Filter {
    /**
     * Constructor of the Filter class
     * @param {HTMLElement} element - The parent HTMLElement of the page
     * @returns 
     */
    constructor(element) {
        if (element == null){
            return;
        }

        // console.error(element);
        this.pagination = element.querySelector('.js-filter-pagination');
        this.content = element.querySelector('.js-filter-content');
        this.sortable = element.querySelector('.js-filter-sortable');
        this.form = element.querySelector('.js-filter-form');
        this.count = element.querySelector('.js-filter-count');
        this.page = parseInt(new URLSearchParams(window.location.search).get('page') || 1);
        this.showMore = this.page === 1 && this.page < parseInt(this.content.dataset.totalPage);
        this.bindEvents();

        // console.error(this.pagination, this.content, this.form, this.sortable, this.count);
    }
/**
 * Function for add all events listener on the page
 */
    bindEvents() {
     const clickEventListener = (e) => {
        // e.preventDefault();
       if(e.target.tagName === 'A' || e.target.tagName === 'I' || e.target.tagName ==='SPAN'){
            e.preventDefault();
            let url;

            if(e.target.tagName === 'I' || e.target.tagName ==='SPAN'){
                url = e.target.closest('a').href;
            } else {
                url = e.target.href;
            }

            this.loadUrl(url);
        }
     }

     if(this.showMore){
        this.pagination.innerHTML = `
        <div class="text-center">
        <button class="btn btn-primary text-center">Voir plus</button>
        </div>`;

        this.pagination.querySelector('button').addEventListener('click', this.loadMore.bind(this));
     } else {
        this.pagination.addEventListener('click', clickEventListener);
     }

      this.sortable.addEventListener('click', clickEventListener);
      
      this.form.querySelectorAll('input[type="text"]').forEach(input => {
        // input.addEventListener('keyup', this.loadForm.bind(this));
        input.addEventListener('keyup', debounce(this.loadForm.bind(this), 300));
      });

      this.form.querySelectorAll('input[type="checkbox"]').forEach(input => {
        input.addEventListener('change', debounce(this.loadForm.bind(this), 500));
      })
    }
/**
 * Function for load ajax request and modify the contenet on the page 
 * @param {string} url  - url for the AJAX request
 * @param {bool} append - If append the content or replace
 */
    async loadUrl(url, append = false){
        this.showLoader();
        const params = new URLSearchParams(url.split('?')[1] || '');
        params.set('ajax', true);

        const response = await fetch(url.split('?')[0] + '?'+ params.toString());

        if(response.status >= 200 && response.status < 300 ){
            const data = await response.json();
           
            this.flipContent(data.content, append);

            if(!this.showMore){
                this.pagination.innerHTML = data.pagination;
            } else if( this.page >= data.totalPage){
                this.pagination.style.display = 'none';
            } else{
                this.pagination.style.display = 'block';
            }

            this.sortable.innerHTML = data.sortable;
            this.count.innerHTML = data.count;
       
            this.hideLoader();
            params.delete('ajax');
            history.replaceState({}, "", url.split('?' )[0]  + '?' + params.toString());
            
        } else {
            console.error(await response.json());
        }
    }
/**
 * Function for load all information on the form and send ahax request
 */
    async loadForm()
    {
        this.page= 1;
        const data = new FormData(this.form);
        const url = new URL(this.form.getAttribute('action') || window.location.href);
        const params = new URLSearchParams();

        data.forEach((value, key) => {
            params.append(key, value);
        });

       return  this.loadUrl(url.pathname + '?' + params.toString());

    //    return this.loadUrl(`${url.pathname}?${params.toString()}`);
    }
/**
 * 
 * @param {*} e 
 */
    async loadMore(e){
        this.page++;

        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        params.set('page', this.page);
        this.loadUrl(url.pathname + '?' + params.toString(), true);
    }

    /*
    * Add animation transition for change content
    * @param {string} content - String with HTML of the new content
    * @param {bool} append - If replace or add the content
    */
   flipContent(content , append) {
        const flipper = new Flipper({element: this.content});
        let cards = this.content.children; //variable qui contient tous les articles

        for( let card of cards){
            flipper.addFlipped({
                element: card,
                flipId: card.id,
            });
        }
    }
/**
 * Show the loader element of the form
 */
    showLoader(){
        this.form.classList.add('is-loading');
        const loader = this.form.querySelector('.js-loading');

        if(loader) {
            loader.setAttribute('arria-hidden', false);
            loader.style.display = "block";
        }
        return;
    }
/**
 * Hide the loader element of the form
 */
    hideLoader(){
        this.form.classList.remove('is-loading');
        const loader = this.form.querySelector('.js-loading');

        if(loader) {
            loader.setAttribute('arria-hidden', true);
            loader.style.display = "none";
        }
        return;

    }
}