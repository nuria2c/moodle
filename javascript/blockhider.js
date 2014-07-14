YUI.add('moodle-theme_cleanudem-blockhider', function(Y) {

/**
 * Class blockhider for Clean UdeM theme.
 * Init this class by calling M.theme_cleanudem.init_block_hider
 */
var blockhider = function() {
    blockhider.superclass.constructor.apply(this, arguments);
};
blockhider.prototype = {
    initializer : function(config) {
        this.set('block', '#' + this.get('id'));
        var b = this.get('block'),
            c = b.one('.content'),
            t = b.one('.title'),
            a = null;
        if (t && (a = t.one('.block_action')) && !a.one('img') && !t.hasClass('ui-mobile-title')) {
            var hide = Y.Node.create('<img class="block-hider-hide" tabindex="0" alt="' + config.tooltipVisible + '" title="' + config.tooltipVisible + '" />');
            hide.setAttribute('src', this.get('iconVisible')).on('click', this.updateState, this, true);
            hide.on('keypress', this.updateStateKey, this, true);
            var show = Y.Node.create('<img class="block-hider-show" tabindex="0" alt="' + config.tooltipHidden + '" title="' + config.tooltipHidden + '" />');
            show.setAttribute('src', this.get('iconHidden')).on('click', this.updateState, this, false);
            show.on('keypress', this.updateStateKey, this, false);
            t.on('dblclick', this.updateState, this);
            a.insert(show, 0).insert(hide, 0);
        }
        c.setStyle('overflow','hidden');

        var anim = new Y.Anim({
            node: b.one('.content'),
            from: {
                opacity: 0,
                height: 0
            },
            to: {
                opacity: 1,
                height: function(node) {
                    var p1 = parseFloat(node.getComputedStyle('paddingTop').replace(/[A-Za-z$-]/g, ""));
                    var p2 = parseFloat(node.getComputedStyle('paddingBottom').replace(/[A-Za-z$-]/g, ""));
                    return (node.get('scrollHeight') - p1 - p2);
                }
            },

            easing: Y.Easing.easeOut,
            duration: 0.2
        });
        anim.on('end',function(e ,a ,b, c) {
            c.setStyle('display','');
            if (b.hasClass('hidden') && !b.get('parentNode').get('parentNode').hasClass('has_dock')) {
                c.setStyle('height','0');
                c.setStyle('opacity','0');
            }else{
                c.setStyle('height','');
                c.setStyle('opacity','');
            }
        },anim,a,b,c);
        this.set('anim', anim);
    },
    updateState : function(e, hide) {
        e.preventDefault();
        this.clearSelection();
        var a = this.get('anim'),
            b = this.get('block'),
            c = b.one('.content'),
            t = b.one('.title');
        if(hide == null) {
            hide = !b.hasClass('hidden');
            t.blur();
        }
        M.util.set_user_preference(this.get('preference'), hide);
        c.setStyle('display','block');
        b.toggleClass('hidden');
        a.set('reverse', hide);
        a.run();
        e.stopPropagation();
    },
    clearSelection : function() {
        if(document.selection && document.selection.empty) {
            document.selection.empty();
        } else if(window.getSelection) {
            var sel = window.getSelection();
            sel.removeAllRanges();
        }
    },
    updateStateKey : function(e, hide) {
        if (e.keyCode == 13) { //allow hide/show via enter key
            this.updateState(this, hide);
        }
    }
};
Y.extend(blockhider, Y.Base, blockhider.prototype, {
    NAME : 'Clean UdeM blockhider',
    ATTRS : {
        id : {},
        preference : {},
        iconVisible : {
            value : M.util.image_url('t/switch_minus_white', 'moodle')
        },
        iconHidden : {
            value : M.util.image_url('t/switch_plus_white', 'moodle')
        },
        block : {
            setter : function(node) {
                return Y.one(node);
            }
        }
    }
});

M.theme_cleanudem = M.theme_cleanudem || {};
M.theme_cleanudem.init_block_hider = function(config) {
    return new blockhider(config);
};

}, '@VERSION@', {requires:['base','node','anim']});