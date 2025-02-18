var config = {
    paths: {
        'logs-tree': 'Cloudflex_LogsManagement/js/logs-tree',
        'content-view': 'Cloudflex_LogsManagement/js/content-view'
    },
    shim: {
        'content-view': {
            deps: ['logs-tree']
        }
    }
};
