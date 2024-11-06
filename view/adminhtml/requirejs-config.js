var config = {
    paths: {
        'logs-tree': 'QubaByte_LogsManagement/js/logs-tree',
        'content-view': 'QubaByte_LogsManagement/js/content-view'
    },
    shim: {
        'content-view': {
            deps: ['logs-tree']
        }
    }
};
