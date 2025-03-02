document.addEventListener('DOMContentLoaded', function() {
    let tree = document.querySelector('.tree');
    let tree_items = tree.querySelectorAll('.list__item');
    for (let i = 0; i < tree_items.length; i++) {
        let item = tree_items[i];
        let is_active = item.querySelectorAll('.active').length;
        if (is_active) {
            let accordeon = item.querySelector('.accordeon');
            if (accordeon) {
                accordeon.classList.add('accordeon_show');
            }
            if ( ! item.classList.contains('tree__dir')) {
                item.scrollIntoView();
                // item.scrollBy({
                //     // top: item.getBoundingClientRect().top,
                //     top: 20000,
                // });
                // item.scrollTop = 100;
                // item.scrollIntoView();
                // console.log(i);
                // console.log(item);
                // console.log(item.getBoundingClientRect().top);
            }
        }
    }
    let accordeons = document.querySelectorAll('.accordeon');
    for (let i = 0; i < accordeons.length; i++) {
        let accordeon = accordeons[i];
        let active_class = 'accordeon_show';
        let cookie_class = getCookie(accordeon.dataset.acc_id);
        if ( cookie_class != '' ) {
            accordeon.classList.add( cookie_class );
        }
        accordeon.addEventListener('click', function (event) {
            if (this.classList.contains(active_class)) {
                this.classList.remove(active_class);
                delCookie(this.dataset.acc_id);
            } else {
                this.classList.add(active_class);
                setCookie(this.dataset.acc_id, active_class);
            }
        });
    }
    document.addEventListener('click', function (event) {
        var trgt = event.target;
        if ( trgt.classList.contains('confirm') ) {
            let approv = confirm(trgt.dataset.confirm);
            if ( ! approv ) {
                event.preventDefault();
            }
        }
    });
    let form = document.querySelector('.form');
    form.addEventListener('keydown', function (event) {
        var trgt = event.target;
        if ( event.key == 'Tab' && trgt.classList.contains('form__textarea_active-tab') ) {
            event.preventDefault();
            let start = trgt.selectionStart;
            let end = trgt.selectionEnd;
        
            // set textarea value to: text before caret + tab + text after caret
            trgt.value = trgt.value.substring(0, start) + "\t" + trgt.value.substring(end);
        
            // put caret at right position again
            trgt.selectionStart = trgt.selectionEnd = start + 1;
        }
    });
});
function getCookie(name) {
  let matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : '';
}
function setCookie(name, value) {
	var cookie_date = new Date(new Date().getTime() + 60 * 60 * 24 * 1000);
    document.cookie = name + "=" + value + "; path=/; expires=" + cookie_date.toUTCString();
}
function delCookie(name) {
    document.cookie = name + "=; path=/;";
}