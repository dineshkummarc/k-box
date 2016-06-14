<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Microsites related Language Lines
	|--------------------------------------------------------------------------
	*/

	'page_title' => 'Микросайты Проектов',
	'page_title_with_name' => 'Микросайт Проекта :project',
    
    'pages' => [
        'create' => 'Создать ":project"',
        'edit' => 'Изменить ":project"',
    ],
    
    
    'hints' => [
        'what' => 'Позволяет создание общедоступной информационной страницы о вашем проекте',
        'create_for_project' => 'Создать микросайт',
        'for_project' => 'Создать микросайт',
        'delete_microsite' => 'Удалить микросайт',
        'edit_microsite' => 'Изменить настройки и содержание страницы микросайта',
        
        'site_title' => 'Название вебсайта, который будет показан пользователям',
        'slug' => 'Данная версия URL поможет пользователям легко найти и запомнить адрес страницы. Адрес не может начинаться со слова "создать".',
        'logo' => 'Разрешенный максимальный размер логотипа 280x80 пикселей. Картинка должна быть доступна по защищенному соединению HTTPS',
        'default_language' => 'Страница будет показана на данном языке, если изначально не задан пользователем',

        'content' => 'Здесь вы можете определить текстовое содержание страницы, а также при желании навигационное меню. На данный момент содержание поддерживается на русском и английском языках.',
        
        'page_title' => 'Название страницы. По умолчанию направляет на главную страницу.',
        'page_slug' => 'Данная версия URL ориентирована на пользователей и поможет им легко найти и запомнить адрес вебсайта.',
        'page_content' => 'Здесь вы можете вставить текст, ссылки и текст другого вида. Поддерживаемый формат ссылается на язык верстки. Вы также можете вставить ссылки и прикрепить элементы из других вебсайтов. Например, вы можете прикрепить содержание рассылки RSS, вставив данный код на соответствующей строке @rss:https://klinktest.wordpress.com/feed/. Пожалуйста, имейте ввиду, что содержание будет кэшировано для предотвращения интенсивного использования ресурсов. Промежутки кэширования будут длиться от 1 до 4 часов (в зависимости от услуг)',
    ],
    
    'actions' => [
        'create' => 'Создать',
        'edit' => 'Изменить',
        'save' => 'Сохранить',
        'delete' => 'Удалить',
        'delete_ask' => 'Вы собираетесь удалить микросайт ":title". Вы уверенны, что хотите продолжить?',
        'view_site' => 'Просмотреть',
        'publish' => 'Создать',
        'view_project_documents' => 'Пройти в Проект',
        'search' => 'Искать в K-Link...',
        'search_project' => 'Искать :project...',
    ],
    
    'messages' => [
        'created' => '":title" был создан и доступен по адресу <a href=":site_url" target="_blank">:slug</a>',
        'updated' => 'Данные ":title" были обновлены',
        'deleted' => '":title" был удален. Адрес микросайта перестанет быть доступным',
    ],
    
    'errors' => [
        'create' => 'Возникла проблема при создании. :error',
        'create_no_project' => 'Пожалуйста, определите содержание. При отсутствии Проекта, микросайт не может быть создан.',
        'create_already_exists' => 'Микросайт для проекта ":project" уже существует. Вы не можете создать больше одного микросайта для каждого проекта.',
        'delete' => 'При удалении возникла ощибка. :error',
        'update' => 'При обновлении данных возникла ощибка. :error',
        'delete_forbidden' => 'Вы не можете удалить ":title" в связи с тем, что вы не являетесь Администратором данного проекта.',
        'forbidden' => 'Для взаимодействия с микросайтами, вы должны обладать правами Администратора Проекта.',
        'user_not_affiliated_to_an_institution' => 'Вы не принадлежите к какой-либо Организации. Прежде чем продолжить создание микросайта, пожалуйста, обратитесь к Администратору для изменения настроек вашего аккаунта.',
    ],
	
    'labels' => [
        'microsite' => 'Микросайт<sup>beta</sup>',
        'site_title' => 'Название',
        'slug' => 'Дружелюбный адрес',
        'site_description' => 'Описание',
        'logo' => 'Логотип вебсайта. Пожалуйста, введите URL картинки',
        'default_language' => 'Язык по умолчанию',
        'cancel_and_back' => 'Отменить',
        'publishing_box' => 'Создание сайта',
        'content' => 'Содержание',
        
        'content_en' => 'Анлийская версия',
        'content_ru' => 'Русская версия',
        
        'page_title' => 'Название страницы для создания',
        'page_slug' => 'Адрес',
        'page_content' => 'Содержание',
    ],
];
