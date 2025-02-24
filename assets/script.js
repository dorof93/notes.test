window.checkInput = checkInput;

function checkInput(input) {
    var input_minlength = input.getAttribute('minlength');
    if (input.value.length < input_minlength) {
        show_error(input);
        input.parentNode.querySelector('.form__error-counter').textContent = input.value.length+'/'+input_minlength;
    } else if ( input.dataset.validate == 'link' && ! validate_link(input.value) ) {
        show_error(input);
    } else {
        remove_error(input);
    }
}
function validate_link(value) {
    const expression = /[-a-zA-Z0-9а-яёА-ЯЁ@:%._\+~#=]{1,256}\.[a-zA-Z0-9а-яёА-ЯЁ()]{1,6}\b([-a-zA-Z0-9а-яёА-ЯЁ()@:%_\+.~#?&//=]*)/gi;
    const regex = new RegExp(expression);
    
    if (value.match(regex) === null) {
        return false;
    }
    return true;
}
function show_error(field) {
    field.parentNode.querySelector('.form__error').classList.add("form__error_active");
}
function remove_error(field) {
    field.parentNode.querySelector('.form__error-counter').textContent = '';
    field.parentNode.querySelector('.form__error').classList.remove("form__error_active");
}