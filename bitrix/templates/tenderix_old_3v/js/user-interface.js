var UI = {
	mso: false, // служебная переменная для ms

	ms: function(o, l){
		var obj = $(o);
		if(!obj.is('textarea') && !obj.is('input'))
			return false;
		var container = this.msGetContainer();

		this.mso = {
			o: o,
			l: l
		};

		obj.on('blur', function(){
			UI.msClearChecker();
		});

		obj.attr('maxlength', l);
		this.msGetContainer().show();
		this.msCheck();
	},

	// Проверяет длину строки в поле и обрезает при необходимости
	msCheck: function(){
		if(this.mso == false)
			return;

		var container = this.msGetContainer();
		var obj_offset = $(this.mso.o).offset();

		container.css('top', (obj_offset.top-22) + 'px')
		container.css('left', (obj_offset.left+$(this.mso.o).width()-43) + 'px')

		if(this.mso.o.value.length > this.mso.l){
			container.html(0);
			this.mso.o.value = this.mso.o.value.substr(0, this.mso.l);
			setTimeout("$(UI.mso.o).addClass('h')");
		}else if(this.mso.o.value.length <= this.mso.l){
			container.html(this.mso.l - this.mso.o.value.length);
		}

		this.mso.timer = setTimeout("UI.msCheck()", 1000);
	},

	// Убивает ограничитель длины строки
	msClearChecker: function(){
		if(this.mso == false)
			return;
		if(typeof(this.mso.timer) != "undefined")
			clearTimeout(this.mso.timer);
		this.mso.o.onblur = function(){};
		this.mso = false;
		this.msGetContainer().hide();
	},

	// Возвращает (если нет — создаёт) контейнер для вывода количества символов
	msGetContainer: function(){
		var cname = 'ms-length-container';
		var c = $('#' + cname);
		if(!c.is('div')){
			console.log(1);
			c = $('<div id="'+cname+'" />');
			c.appendTo('body');
			c.css('position', 'absolute')
				.css('background', 'white')
				.css('border', '1px solid silver')
				.css('border-radius', '9px')
				.css('padding', '2px 10px 1px 10px')
				.css('font-size', '12px')
				.css('z-index', '1060')
				.css('width', '30px')
				.css('text-align', 'center')
				.css('display', 'none')
				.css('cursor', 'default')
		}
		return c;
	},

	// Методы для вывода сообщений на экран
	message: function(options){
		if(!options)
			return;
		if(typeof(options) != "object")
			return console.log('ERROR: метод `message` может принимать в качестве параметра только объект.'), false;

		var close = $('<i class="icon-close">×</i>');
		var container = $('<div class="pop-up-message" />');
		var text = $('<div/>').html(options.text);
		var id = 'popupmessage' + new Date().getTime();
		var veil;

		if(options.group){
			container.attr('group', options.group);
			this.closeMessages(options.group);
		}

		// Veil
		if(options.veil){
			veil = $('#veil');
			if(!veil.is('div')){
				veil = $('<div id="veil"/>');
				veil.appendTo('body');
			}
			veil.fadeIn(250);
			container.addClass('with-veil');
		}else
			container.removeClass('with-veil');

		container.attr('id', id);
		if(!options.noClose)
			close.appendTo(container);
		cose = container.find('i').first();
		container
			.css('position', 'absolute')
			.css('left', '-9999px');
		text.appendTo(container);
		container.appendTo('body');
		container
			.css('margin-left', '-' + (container.width() / 2) + 'px')
			.css('margin-top', '-' + (container.height() / 2) + 'px')
			.css('position', 'fixed')
			.css('left', '50%')
			.hide()
			.fadeIn(200);

		close.click(function(){
			setTimeout('$("#' + id + '").remove()', 100);
			setTimeout('$("#veil").fadeOut(100)', 100);
		});

		if(!options.timer)
			options.timer = 5000;

		if(options.veil)
			setTimeout('$("#veil").fadeOut(250)', options.timer-100);
		setTimeout('$("#'+id+'").fadeOut(100)', options.timer-100);
		setTimeout('$("#'+id+'").remove()', options.timer);

		return container;
	},

	closeMessages: function(group){
		var messages;
		if(group)
			messages = $('.pop-up-message[group="'+group+'"]');
		else
			messages = $('.pop-up-message');

		messages.remove();
		$("#veil").hide();
	},

	// Возвращает целое
	intval: function(i){
		if(isNaN(parseInt(i)))
			return 0;
		else
			return parseInt(i);
	},

	ge: function(s){
		return document.getElementById(s);
	},

	w: function(i, s){
		if(!s) s = ['шт', 'шт', 'шт'];
		i = i.toString();
		var o = i.slice(-1);
		var t = (i.length > 1) ? i.slice(-2): '';
		if((t > 10 && t < 15) || o == 0 || (o > 4 && o != 1)) return i + ' ' + s[2];
		if(o > 1 && o < 5) return i + ' ' + s[1];
		if(o == 1) return i + ' ' + s[0];
	}
}