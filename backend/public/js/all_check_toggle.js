// jQuery
// あるcheckboxに合わせて全checkedのtoggleをする関数
function all_checkbox_toggle(button_selector, target_selector) {
    $(button_selector).on("click", function () {
        $(target_selector).prop("checked", this.checked);
    });
}

// jsバージョン
function all_checkbox_toggle_js(prop) {
    this.checked = prop.checked;
    Array.from(document.getElementsByClassName("checkbox")).forEach(
        (element) => (element.checked = this.checked)
    );
}
