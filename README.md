
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


       // {
    //     "title": "Kho hàng",
    //     "icon": "fas fa-warehouse",
    //     "id": "warehouses",
    //     "children": [
    //         {
    //             "title": "Quản lý vật liệu",
    //             "url": "admin.materials.index"
    //         },
    //         {
    //             "title": "Quản lý phiếu nhập kho",
    //             "url": "admin.material-imports.index"
    //         },
    //         {
    //             "title": "Quản lý công nợ",
    //             "url": "admin.suppliers-debts.index"
    //         },
    //         {
    //             "title": "Quản lý nhà cung cấp",
    //             "url": "admin.suppliers.index"
    //         }
    //     ],
    //     "inRoutes": [
    //         "admin.suppliers.index",
    //         "admin.materials.index",
    //         "admin.material-imports.index",
    //         "admin.material-imports.create",
    //         "admin.suppliers-debts.index"
    //     ]
    // },

