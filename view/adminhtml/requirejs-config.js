var config = {
    paths: {
        'logs-tree': 'NetBytes_LogsExplorer/js/logs-tree',
        'log-view': 'NetBytes_LogsExplorer/js/log-view'
    },
    shim: {
        'log-view': {
            deps: ['logs-tree']
        }
    }
};
