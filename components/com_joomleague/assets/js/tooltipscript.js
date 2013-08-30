/**
 * Main JavaScript file
 *
 * @package         Tooltips
 * @version         3.3.0
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2013 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

function tooltips_init(tip)
{
	var tip = tip;
	tip.fade_in = new Fx.Tween(tip, {
		property: 'opacity',
		'duration': tooltips_fade_in_speed
	});
	tip.fade_out = new Fx.Tween(tip, {
		property: 'opacity',
		'duration': tooltips_fade_out_speed,
		onComplete: function(tip) { tooltips_hide_complete(tip) }
	});
	tip.initialized = 1;
}
function tooltips_show(tip)
{
	if (typeof( tip.initialized ) == 'undefined') {
		tooltips_init(tip);
	}
	document.getElements('.tooltips-tip').each(function(el)
	{
		el.setStyle('display', 'none');
	});
	if (( tip.getElement('img') && tip.getElement('img').getStyle('width').toInt() > tooltips_max_width )
		|| ( tip.getElement('table') && tip.getElement('table').getStyle('width').toInt() > tooltips_max_width )
		) {
		tip.getElement('div.tip').setStyle('max-width', 'none');
	} else {
		tip.getElement('div.tip').setStyle('max-width', tooltips_max_width);
	}
	tip.setStyle('display', 'block');
	tip.fade_in.cancel();
	tip.fade_out.cancel();
	tip.fade_in.start(1);
}
function tooltips_hide(tip)
{
	if (typeof( tip.initialized ) == 'undefined') {
		tip.setStyle('display', 'none');
	} else {
		tip.fade_in.cancel();
		tip.fade_out.cancel();
		tip.fade_out.start(0);
	}
}
function tooltips_hide_complete(tip)
{
	tip.setStyle('display', 'none');
}
