
 {
        "title": "Vật liệu",
        "icon": "fa-solid fa-box",
        "id": "materials",
        "children": [
            {
                "title": "Quản lý vật liệu",
                "url": "admin.materials.index"
            },
            {
                "title": "Quản lý nhập kho",
                "url": "admin.warehouse.index"
            }
        ],
        "inRoutes": [
            "admin.materials.index",
            "admin.warehouse.index",
            "admin.materials.show"
        ]
    },
 {
    "title": "Phân quyền",
    "icon": "fas fa-user-shield",
    "id": "permissions",
    "children": [
    {
    "title": "Quản lý vai trò",
    "url": "admin.roles.index"
    },
    {
    "title": "Quản lý quyền hạn",
    "url": "admin.permissions.index"
    }
    ],
    "inRoutes": [
    "admin.employees.index",
    "admin.roles.index",
    "admin.permissions.index",
    "admin.activity.log.history"
    ]
    },


 

            {
                "title": "Quản lý yêu cầu vật tư",
                "url": "admin.material-requests.index"
            },


,
    {
        "title": "Kế toán",
        "icon": "fas fa-calculator",
        "url": "javascript:void(0)",
        "id": "cashbook",
        "children": [
            {
                "title": "Thu chi tiền mặt",
                "url": "admin.cashbook"
            }
        ],
        "inRoutes": ["admin.cashbook"]
    }
