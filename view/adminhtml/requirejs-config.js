var config = {
    paths: {
        'logs-tree': 'NetBytes_LogsExplorer/js/logs-tree',
        'content-view': 'NetBytes_LogsExplorer/js/content-view'
    },
    shim: {
        'content-view': {
            deps: ['logs-tree']
        }
    }
};
