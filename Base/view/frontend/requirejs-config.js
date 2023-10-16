var config = {
    map: {
        '*': {
            masonry : 'Mageplus_Base/js/masonry.min',
            wowjs: 'Mageplus_Base/js/wow.min',
            matchHeight: 'Mageplus_Base/js/matchHeight'
        }
    },
    paths: {
        'Mageplus/Base/lightcase': 'Mageplus_Base/js/lightcase.min'
    },
    shim: {
        "Mageplus/Base/lightcase": ["jquery"],
        'masonry': {
            deps: ['jquery']
        },
        'wowjs': {
            deps: ['jquery']
        },
        'matchHeight': {
            deps: ['jquery']
        }
    }
};
