function createCustomCheckbox(label, name, value = 'Y', elem = false, textToLabel = false, textAfterLabel = false, appendClass = '') {

	let wrapper = document.createElement('label'),
		input = document.createElement('input'),
		spanLabel = document.createElement('span'),
		textNode = document.createTextNode('');

	wrapper.setAttribute('class', 'param-label custom-creator '+appendClass);

	input.setAttribute('type', 'checkbox');
	input.setAttribute('name', name);
	input.setAttribute('id', name);
	input.setAttribute('value', value);
	input.setAttribute('required', '');

	if (elem) {

		input = elem;
		$(wrapper).addClass(`custom-creator-${$(input).prop('type')}`);
	}

	input.setAttribute('class', 'hide');

	spanLabel.setAttribute('class', 'param-text');
	spanLabel.innerText = label;

	wrapper.appendChild(input);
	wrapper.appendChild(spanLabel);

	if (textToLabel) {

		textNode.appendData(textToLabel);
		wrapper.insertBefore(textNode, spanLabel);
	}

	if (textAfterLabel) {

		textNode.appendData(textAfterLabel);
		wrapper.appendChild(textNode);
	}

	return wrapper;
}

//принимает форму и массив инпутов вставляет их в форму
//Если форму не передать собирает массив инпутов из переданного массива formItems
function formConstructor(selfForm, formItems) {

	let resultItems = [];

	for (let item of formItems) {

		let resultElement = document.createElement('input');

		if (item.element === 'select') {
			resultElement = document.createElement(item.element);

			if (item.class) {
				resultElement.setAttribute('class', item.class + ' select-custom');
			} else {
				resultElement.setAttribute('class', 'select-custom');
			}
		}

		if (item.name) {
			resultElement.setAttribute('name', item.name);
		}

		if (item.placeholder) {

			resultElement.setAttribute('placeholder', item.placeholder);
		}

		if (item.type) {

			resultElement.setAttribute('type', item.type);
		}

		if (item.value) {

			resultElement.setAttribute('value', item.value);
		}

		if (item.onclick) {

			resultElement.onclick = item.onclick;
		}

		if (item.required) {

			resultElement.setAttribute('required', '');
		}

		if (item.check) {

			resultElement.setAttribute('checked', '');
		}

		if (item.data) {

			resultElement.setAttribute(item.data.title, item.data.value);
		}

		if (item.hidden) {

			resultElement.setAttribute('type', 'hidden');
		}

		if (item.options) {

			for (let option of item.options) {

				let elOption = document.createElement('option');

				elOption.innerText = option.text;
				elOption.value = option.value;

				if (option.selected) {
					elOption.setAttribute('selected', '');
				}

				if (option['data-item']) {
					elOption.setAttribute('data-item', option['data-item']);
				}

				resultElement.appendChild(elOption);
			}
		}

		if (item.label) {
			let textToLabel = item.textToLabel,
				textAfterLabel = item.textAfterLabel;
			appendClass = item.appendClass;

			resultElement = createCustomCheckbox(
				item.label,
				resultElement.getAttribute('name'),
				false,
				resultElement,
				textToLabel,
				textAfterLabel,
				appendClass
			);
		}

		if (selfForm) {

			selfForm.appendChild(resultElement);
		} else {

			resultItems.push(resultElement)
		}
	}

	if (resultItems.length > 0) {

		return resultItems
	}
}


/**
 *{
 *    @param formHeader
 *    @param enctype
 *    @param method
 *    @param formName
 *    @param formItems
 *    @param popupContentStyles
 *    @param styleH3
 *    @param action
 *    @param closeButton
 *}
 * @param onBeforeFormRemove
 * @param onBeforeCreateForm
 * @param onAfterCreateForm
 * @param onBeforeSubmitForm
 * @returns {HTMLElement}
 */
function appendUserForm({
							formHeader,
							enctype = null,
							method,
							formName,
							formItems,
							popupContentStyles = null,
							styleH3 = '',
							action = null,
							closeButton = null,
						}, onBeforeFormRemove, onBeforeCreateForm, onAfterCreateForm, onBeforeSubmitForm) {
	let rootPopup = document.createElement('div'),
		popupContent = document.createElement('div'),
		popupForm = document.createElement('form'),
		headForm = document.createElement('h3'),
		needFormConstruct = true;

	if (closeButton !== null) {
		let link = document.createElement('a'),
			spanL = document.createElement('span'),
			spanR = document.createElement('span');

		link.setAttribute('href', 'javascript:void(0)');
		link.setAttribute('class', 'close-popup onclose');

		link.onclick = (e) => {
			rootPopup.onclick(e);
		};

		link.appendChild(spanL);
		link.appendChild(spanR);
		popupContent.appendChild(link);
	}

	if (action !== null) {
		popupForm.setAttribute('action', action);
	}

	if (popupContentStyles !== null) {
		popupContent.style = popupContentStyles
	}

	if (enctype !== null) {

		popupForm.setAttribute('enctype', enctype);
	}

	if (styleH3.length > 0) {
		headForm.style = styleH3;
	}

	let rootPopupEventDefault = function (e) {

			let cancelledRemoveForm = false;

			if (typeof onBeforeFormRemove === 'function') {

				cancelledRemoveForm = onBeforeFormRemove(popupForm);
			}

			if (cancelledRemoveForm) {
				return;
			}

			$(this).detach();
		},
		popupContentEventDefault = function (e) {
			e.stopPropagation();
		},
		onSubmitFormDefault = function (e) {

			let result = true;

			if (typeof onBeforeSubmitForm === 'function') {

				result = onBeforeSubmitForm(e, (e) => rootPopup.onclick(e));
			}

			return result;
		};

	headForm.innerText = formHeader;
	popupForm.method = method;
	popupForm.name = formName;
	popupForm.onsubmit = onSubmitFormDefault;

	rootPopup.setAttribute('class', 'root_popup');
	rootPopup.onclick = rootPopupEventDefault;

	popupContent.setAttribute('class', 'popup-content');
	popupContent.onclick = popupContentEventDefault;

	if (typeof onBeforeCreateForm === 'function') {

		let form = onBeforeCreateForm(popupForm);

		if (form) {

			popupForm = form;
			needFormConstruct = false;
		}
	}

	if (needFormConstruct) {

		formConstructor(popupForm, formItems);
	}

	popupContent.appendChild(headForm);
	popupContent.appendChild(popupForm);
	rootPopup.appendChild(popupContent);

	if (typeof onAfterCreateForm === 'function') {

		onAfterCreateForm(popupForm);
	}

	document.body.appendChild(rootPopup);

	if ($(window).height() < $(popupContent).height()) {

		rootPopup.style = 'overflow-x: scroll; align-items: flex-start;'
	}

	return popupForm;
}

let adminAuthField = [
	{
		type: 'text',
		name: 'login',
		placeholder: 'login',
		required: true
	},
	{
		type: 'password',
		name: 'pass',
		placeholder: 'password',
		required: true
	},
	{
		type: 'submit',
		value: 'go'
	}
];

let logoutField = [
	{
		type: 'submit',
		value: 'allow',
		name: 'isAllow'
	}
];

$(function () {

	$('.login-admin').on('click', function () {

		let addFirmForm = {
			formHeader: 'Авторизация для админа',
			method: 'post',
			formName: 'adminAuth',
			formItems: adminAuthField,
		};
		appendUserForm(
			addFirmForm,
			false,
			false,
			false,
			(e) => {

				$.post(
					'/?PAGE=user&ACTION=userAuth',
					$('form[name=adminAuth]').serializeArray(),
					function (data) {

						if (data.success) {

							window.location.reload();
						} else {

							$('.error').remove();
							$('form[name=adminAuth]').prev('h3').after(`<div class="error">${data.error}</div>`);
						}

					},
					'json'
				);

				return false;
			}
		)
	});

	$('.logout').on('click', function () {


		let logoutForm = {
			formHeader: 'Вы уверены что хотите выйти ?',
			method: 'post',
			formName: 'logoutForm',
			formItems: logoutField,
		};
		appendUserForm(
			logoutForm,
			false,
			false,
			false,
			(e) => {

				$.post(
					'/?PAGE=user&ACTION=logout',
					$('form[name=logoutForm]').serializeArray(),
					function (data) {

						if (data.success) {

							window.location.reload();
						} else {

							$('.error').remove();
							$('form[name=adminAuth]').prev('h3').after(`<div class="error">${data.error}</div>`);
						}

					},
					'json'
				);

				return false;
			}
		)
	});

	$('.sort').on('click', function (evt) {

		evt.preventDefault();
		let $this = $(this),
			searchStr = window.location.search.length > 0 ? window.location.search : '';

		$.post(
			'/' + searchStr,
			{
				by: $this.attr('href').replace('#', ''),
				direction: $this.data('type'),
				sort: 'Y'
			},
			function (data) {

				let $data = $(data);

				if ($data.find('.todos-list').length > 0) {

					$this.data('type', $this.data('type') == 'desc' ? 'asc' : 'desc');
					$('.todos-list').html($data.find('.todos-list').children());
				}
			}
		);
	});

	$('.edit-todo').on('click', function () {

		let $thisLink = $(this),
			arInputs = [
				{
					name: 'edit_todo[status_id]',
					type: 'radio',
					value: '1',
					label: 'в работе',
					required: 'required'
				},
				{
					name: 'edit_todo[status_id]',
					type: 'radio',
					value: '2',
					label: 'выполнено',
					required: 'required',
				},
				{
					type: 'submit',
					value: 'change'
				}
			];
		let $itemTodo = $(this).parent();

		$itemTodo.children().each(function () {

			let $this = $(this);

			if ($this.data('edit') === 'Y') {

				let translit = {

					'ТЕКСТ': 'text',
				};

				let arSplit = $this.text().split(':'),
					item = {},
					translateName = translit[arSplit[0].toUpperCase()];

				item.name = `edit_todo[${translateName}]`;
				item.value = arSplit[1].trim();
				item.placeholder = translateName;
				item.required = true;

				arInputs.splice(0, 0, item);
			}

			if ($this.data('status') > 0) {

				for(let item of arInputs) {

					if ($this.data('status') == item.value) {

						item.check = true;
					}
				}
			}
		});

		if (arInputs.length > 0) {

			let editTodo = {
				formHeader: 'Редактирование задачи',
				method: 'post',
				formName: 'editForm',
				formItems: arInputs,
			}, hiddenInput = {
				name: 'edit_todo[id]',
				type: 'hidden',
				value: $thisLink.data('id')
			};

			arInputs.splice(0, 0, hiddenInput);

			appendUserForm(
				editTodo,
				false,
				false,
				false,
				(e) => {

					$.post(
						'/?PAGE=todos&ACTION=changeTodo',
						$('form[name=editForm]').serializeArray(),
						function (data) {

							if (data.success) {

								window.location.reload();
							} else {

								$('.error').remove();
								$('form[name=editForm]').prev('h3').after(`<div class="error">${data.error}</div>`);
							}

						},
						'json'
					);

					return false;
				}
			)
		}
	})

}); //$(function () {