/**
 * weaver_hide_css, JavaScript specialized hide table row
 *
 * @version 1.1
 * @license GNU Lesser General Public License, http://www.gnu.org/copyleft/lesser.html
 * @author  Bruce Wampler
 */
function wvr_ToggleRowCSS(his, me, show, hide) {
    if (his.style.display != 'none') {
        his.style.display = 'none';
        me.innerHTML = '<img src="' + show + '" />';
    } else {
        his.style.display = 'table-row';
        me.innerHTML = '<img src="' + hide + '" />';
    }
}
function wvr_ToggleDIV(his, me, show, hide) {
    if (his.style.display != 'none') {
        his.style.display = 'none';
        me.innerHTML = '<img src="' + show + '" />';
    } else {
        his.style.display = 'block';
        me.innerHTML = '<img src="' + hide + '" />';
    }
}
