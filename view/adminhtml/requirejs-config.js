var config = {
    paths: {
        'logs-tree': 'NetBytes_LogsManagement/js/logs-tree',
        'content-view': 'NetBytes_LogsManagement/js/content-view'
    },
    shim: {
        'content-view': {
            deps: ['logs-tree']
        }
    }
};
