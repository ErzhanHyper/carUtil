let color1 = '#5f5f74';

export default {
    items: [
        {
            name: 'Заявки',
            caption: 'по сертификатам',
            icon: 'home',
            color: color1,
            expand: true,
            link: '/preorder',

            children: [
                {
                    name: 'Все заявки',
                    icon: 'article',
                    color: color1,
                    link: '/preorder'
                },
                {
                    name: 'Продажа ТС',
                    icon: 'content_copy',
                    color: color1,
                    link: '/transfer/order'
                }
            ]
        },
        {
            name: 'Сертификаты',
            caption: '',
            icon: 'verified',
            color: color1,
            expand: false,
            link: '/certificate',
        }
    ],


    moderatorItems: [
        {
            name: 'Заявки',
            caption: 'по сертификатам',
            icon: 'home',
            color: color1,
            expand: true,
            link: '/',

            children: [
                {
                    name: 'Предзаявка',
                    icon: 'content_copy',
                    color: color1,
                    link: '/preorder'
                },
                {
                    name: 'Заявки в работе',
                    icon: 'article',
                    color: color1,
                    link: '/order'
                },
            ]
        },

        // {
        //     name: 'Клиенты',
        //     icon: 'people',
        //     color: color1,
        //     expand: false,
        //     link: '/client'
        // },
        {
            name: 'Сервис',
            icon: 'feedback',
            color: color1,
            expand: false,
            link: '/service'
        },
        {
            name: 'Отчеты',
            icon: 'report',
            color: color1,
            expand: false,
            link: '/report'
        },
        {
            name: 'Погашения',
            icon: 'receipt',
            color: color1,
            expand: false,
            link: '/sell'
        },

        {
            name: 'Передачи',
            icon: 'swap_horiz',
            color: color1,
            expand: false,
            link: '/exchange'
        },

        {
            name: 'Производители',
            icon: 'factory',
            color: color1,
            expand: false,
            link: '/manufacture'
        },

        {
            name: 'Заводы',
            icon: 'precision_manufacturing',
            color: color1,
            expand: false,
            link: '/factory'
        },

        {
            name: 'Модели ТС',
            icon: 'directions_car',
            color: color1,
            expand: false,
            link: '/vehicle'
        },

        {
            name: 'Пользователи',
            icon: 'people',
            color: color1,
            expand: false,
            link: '/user'
        },
        {
            name: 'Логи',
            icon: 'error',
            color: color1,
            expand: false,
            link: '/log'
        }
    ],

    operatorItems: [
        {
            name: 'Заявки',
            caption: 'по сертификатам',
            icon: 'home',
            color: color1,
            expand: true,
            link: '/',

            children: [
                {
                    name: 'Заявки в работе',
                    icon: 'article',
                    color: color1,
                    link: '/order'
                },
            ]
        },

        // {
        //     name: 'Клиенты',
        //     icon: 'people',
        //     color: color1,
        //     expand: false,
        //     link: '/client'
        // },
        // {
        //     name: 'Сервис',
        //     icon: 'feedback',
        //     color: color1,
        //     expand: false,
        // },
        {
            name: 'Отчеты',
            icon: 'report',
            color: color1,
            expand: false,
            link: '/report'
        },

    ],

    dealerLightItems: [
        {
            name: 'Погашения',
            icon: 'report',
            color: color1,
            expand: false,
            link: '/sell'
        },

    ],

}
