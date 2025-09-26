<?php


return [
    'default-permission'                                =>  [
        'title'                                         => 'Default Permissions',
        'sections'                                      =>  [
            [
                'title'                                 => 'Currency',
                'routes'                                => [
                    [
                        'title'                         => 'Currency List',
                        'route'                         => 'admin.currency.index'
                    ],

                    [
                        'title'                         => 'Edit',
                        'route'                         => 'admin.currency.edit'
                    ],
                    [
                        'title'                         => 'Update',
                        'route'                         => 'admin.currency.update'
                    ]

                ],
            ],

            [
                'title'                                 => 'Payment Gateway',
                'routes'                                => [
                    [
                        'title'                         => 'Create',
                        'route'                         => 'admin.payment.gateway.create'
                    ],
                    [
                        'title'                         => 'Store',
                        'route'                         => 'admin.payment.gateway.store'
                    ],
                    [
                        'title'                         => 'View',
                        'route'                         => 'admin.payment.gateway.view'
                    ],
                    [
                        'title'                         => 'Edit',
                        'route'                         => 'admin.payment.gateway.edit'
                    ],
                    [
                        'title'                         => 'Update',
                        'route'                         => 'admin.payment.gateway.update'
                    ],
                    [
                        'title'                         => 'Status Update',
                        'route'                         => 'admin.payment.gateway.status.update'
                    ],
                    [
                        'title'                         => 'Remove',
                        'route'                         => 'admin.payment.gateway.remove'
                    ],

                ],
            ],
             [
                'title'                                 => 'Fees',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.trx.settings.index'
                    ],
                    [
                        'title'                         => 'Update',
                        'route'                         => 'admin.trx.settings.charges.update'
                    ],
                ],
            ],
        ],
    ],

    'Logs'                                =>  [
        'title'                                         => 'logs-Permissions',
        'sections'                                      =>  [
            [
                'title'                                 => 'Booking Logs',
                'routes'                                => [
                    [
                        'title'                         => 'Pending Log',
                        'route'                         => 'admin.booking.log.pending'
                    ],

                    [
                        'title'                         => 'Complete Log',
                        'route'                         => 'admin.booking.log.complete'
                    ],
                    [
                        'title'                         => 'Canceled Log',
                        'route'                         => 'admin.booking.log.canceled'
                    ],
                    [
                        'title'                         => 'All Log',
                        'route'                         => 'admin.booking.log.index'
                    ]

                ],
            ],

            [

                'title'                                 => 'Money Out logs',
                'routes'                                => [
                    [
                        'title'                         => 'Pending Log',
                        'route'                         => 'admin.money.out.pending'
                    ],

                    [
                        'title'                         => 'Complete Log',
                        'route'                         => 'admin.money.out.complete'
                    ],
                    [
                        'title'                         => 'Canceled Log',
                        'route'                         => 'admin.money.out.canceled'
                    ],
                    [
                        'title'                         => 'All Log',
                        'route'                         => 'admin.money.out.index'
                    ]

                ],

            ],

        ],
    ],
    'interface-permission'                                         => [
        'title'                                         => 'Interface Panel Permissions',
        'sections'                                      =>  [
            [
                'title'                                 => 'User Care',
                'routes'                                => [
                    [
                        'title'                         => 'User List',
                        'route'                         => 'admin.users.index'
                    ],
                    [
                        'title'                         => 'Active Users',
                        'route'                         => 'admin.users.active'
                    ],
                    [
                        'title'                         => 'Create Users',
                        'route'                         => 'admin.users.create'
                    ],
                    [
                        'title'                         => 'Store Users',
                        'route'                         => 'admin.users.store'
                    ],
                    [
                        'title'                         => 'Banned Users',
                        'route'                         => 'admin.users.banned'
                    ],
                    [
                        'title'                         => 'Email Unverified',
                        'route'                         => 'admin.users.email.unverified'
                    ],
                    [
                        'title'                         => 'Email To Users',
                        'route'                         => 'admin.users.email.users'
                    ],
                    [
                        'title'                         => 'Send Mail To Users',
                        'route'                         => 'admin.users.email.users.send'
                    ],
                    [
                        'title'                         => 'User Details',
                        'route'                         => 'admin.users.details'
                    ],
                    [
                        'title'                         => 'User Details Update',
                        'route'                         => 'admin.users.details.update'
                    ],
                    [
                        'title'                         => 'Login Logs',
                        'route'                         => 'admin.users.login.logs'
                    ],
                    [
                        'title'                         => 'Mail Logs',
                        'route'                         => 'admin.users.mail.logs'
                    ],
                    [
                        'title'                         => 'Send Mail',
                        'route'                         => 'admin.users.send.mail'
                    ],
                    [
                        'title'                         => 'Login as Member',
                        'route'                         => 'admin.users.login.as.member'
                    ],
                    [
                        'title'                         => 'Wallet Balance Update',
                        'route'                         => 'admin.users.wallet.balance.update'
                    ],
                    [
                        'title'                         => 'User Search',
                        'route'                         => 'admin.users.search'
                    ],
                ],
            ],
                 [
                'title'                                 => 'Hospital Care',
                'routes'                                => [
                    [
                        'title'                         => 'Hospital List',
                        'route'                         => 'admin.hospitals.index'
                    ],
                    [
                        'title'                         => 'Active Hospital',
                        'route'                         => 'admin.hospitals.active'
                    ],
                    [
                        'title'                         => 'Create Hospital',
                        'route'                         => 'admin.hospitals.create'
                    ],
                    [
                        'title'                         => 'Store Hospital',
                        'route'                         => 'admin.hospitals.store'
                    ],
                    [
                        'title'                         => 'Banned Hospital',
                        'route'                         => 'admin.hospitals.banned'
                    ],
                    [
                        'title'                         => 'Email Unverified',
                        'route'                         => 'admin.hospitals.email.unverified'
                    ],
                    [
                        'title'                         => 'Email To Hospital',
                        'route'                         => 'admin.hospitals.email.hospitals'
                    ],
                    [
                        'title'                         => 'Send Mail To Hospital',
                        'route'                         => 'admin.hospitals.email.hospitals.send'
                    ],
                    [
                        'title'                         => 'Hospital Details',
                        'route'                         => 'admin.hospitals.details'
                    ],
                    [
                        'title'                         => 'Hospital Details Update',
                        'route'                         => 'admin.hospitals.details.update'
                    ],
                    [
                        'title'                         => 'Login Logs',
                        'route'                         => 'admin.hospitals.login.logs'
                    ],
                    [
                        'title'                         => 'Mail Logs',
                        'route'                         => 'admin.hospitals.mail.logs'
                    ],
                    [
                        'title'                         => 'Send Mail',
                        'route'                         => 'admin.hospitals.send.mail'
                    ],
                    [
                        'title'                         => 'Login as Member',
                        'route'                         => 'admin.hospitals.login.as.member'
                    ],
                    [
                        'title'                         => 'Wallet Balance Update',
                        'route'                         => 'admin.hospitals.wallet.balance.update'
                    ],
                    [
                        'title'                         => 'Hospital Search',
                        'route'                         => 'admin.hospitals.search'
                    ],
                ],
            ],
            [
                'title'                                 => 'Admin Care',
                'routes'                                => [
                    [
                        'title'                         => 'Admin List',
                        'route'                         => 'admin.admins.index'
                    ],
                    [
                        'title'                         => 'Email All Admins',
                        'route'                         => 'admin.admins.email.admins'
                    ],
                    [
                        'title'                         => 'Delete Admin',
                        'route'                         => 'admin.admins.admin.delete'
                    ],
                    [
                        'title'                         => 'Send Email',
                        'route'                         => 'admin.admins.send.email'
                    ],
                    [
                        'title'                         => 'Search',
                        'route'                         => 'admin.admins.search'
                    ],
                    [
                        'title'                         => 'Store',
                        'route'                         => 'admin.admins.admin.store'
                    ],
                    [
                        'title'                         => 'Update',
                        'route'                         => 'admin.admins.admin.update'
                    ],
                    [
                        'title'                         => 'Status Update',
                        'route'                         => 'admin.admins.admin.status.update'
                    ],
                ],
            ],
            [
                'title'                                 => 'Role & Permissions',
                'routes'                                => [
                    [
                        'title'                         => 'Role List',
                        'route'                         => 'admin.admins.role.index'
                    ],
                    [
                        'title'                         => 'Role Store',
                        'route'                         => 'admin.admins.role.store'
                    ],
                    [
                        'title'                         => 'Role Update',
                        'route'                         => 'admin.admins.role.update'
                    ],
                    [
                        'title'                         => 'Role Delete',
                        'route'                         => 'admin.admins.role.delete'
                    ],
                    [
                        'title'                         => 'Permission List',
                        'route'                         => 'admin.admins.role.permission.index'
                    ],
                    [
                        'title'                         => 'Permission Create',
                        'route'                         => 'admin.admins.role.permission.create'
                    ],
                    [
                        'title'                         => 'Permission Store',
                        'route'                         => 'admin.admins.role.permission.store'
                    ],
                    [
                        'title'                         => 'Permission Edit',
                        'route'                         => 'admin.admins.role.permission.edit'
                    ],
                    [
                        'title'                         => 'Permission Update',
                        'route'                         => 'admin.admins.role.permission.update'
                    ],
                    [
                        'title'                         => 'Permission Delete',
                        'route'                         => 'admin.admins.role.permission.delete'
                    ],
                    [
                        'title'                         => 'Permission View',
                        'route'                         => 'admin.admins.role.permission'
                    ],
                ],
            ],
        ],
    ],
    'settings-permission'                                          => [
        'title'                                         => 'Settings Permissions',
        'sections'                                      =>  [
            [
                'title'                                 => 'Web Settings',
                'routes'                                => [
                    [
                        'title'                         => 'Basic Settings',
                        'route'                         => 'admin.web.settings.basic.settings'
                    ],
                    [
                        'title'                         => 'Basic Settings Update',
                        'route'                         => 'admin.web.settings.basic.settings.update'
                    ],
                    [
                        'title'                         => 'Basic Settings Activation Update',
                        'route'                         => 'admin.web.settings.basic.settings.activation.update'
                    ],
                    [
                        'title'                         => 'Image Assets',
                        'route'                         => 'admin.web.settings.image.assets'
                    ],
                    [
                        'title'                         => 'Image Assets Update',
                        'route'                         => 'admin.web.settings.image.assets.update'
                    ],
                    [
                        'title'                         => 'Setup Seo',
                        'route'                         => 'admin.web.settings.setup.seo'
                    ],
                    [
                        'title'                         => 'Seo Update',
                        'route'                         => 'admin.web.settings.setup.seo.update'
                    ],

                ],
            ],
            [
                'title'                                 => 'App Settings',
                'routes'                                => [
                    [
                        'title'                         => 'Splash Screen',
                        'route'                         => 'admin.app.settings.splash.screen'
                    ],
                    [
                        'title'                         => 'Splash Screen Update',
                        'route'                         => 'admin.app.settings.splash.screen.update'
                    ],
                    [
                        'title'                         => 'Onboard Screens',
                        'route'                         => 'admin.app.settings.onboard.screens'
                    ],
                    [
                        'title'                         => 'Onboard Screen Store',
                        'route'                         => 'admin.app.settings.onboard.screen.store'
                    ],
                    [
                        'title'                         => 'Onboard Screen Update',
                        'route'                         => 'admin.app.settings.onboard.screen.update'
                    ],
                    [
                        'title'                         => 'Onboard Screen Status Update',
                        'route'                         => 'admin.app.settings.onboard.screen.status.update'
                    ],
                    [
                        'title'                         => 'Onboard Screen Delete',
                        'route'                         => 'admin.app.settings.onboard.screen.delete'
                    ]
                ],
            ],
            [
                'title'                                 => 'Setup Email',
                'routes'                                => [
                    [
                        'title'                         => 'Email Configuration',
                        'route'                         => 'admin.setup.email.config'
                    ],
                    [
                        'title'                         => 'Email Configuration Update',
                        'route'                         => 'admin.setup.email.config.update'
                    ],
                    [
                        'title'                         => 'Test Mail Send',
                        'route'                         => 'admin.setup.email.test.mail.send'
                    ],
                ],
            ],

            [
                'title'                                 => 'Setup Sections',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.setup.sections.section'
                    ],
                    [
                        'title'                         => 'Update',
                        'route'                         => 'admin.setup.sections.update'
                    ],
                    [
                        'title'                         => 'Item Store',
                        'route'                         => 'admin.setup.sections.item.store'
                    ],
                    [
                        'title'                         => 'Item Update',
                        'route'                         => 'admin.setup.sections.item.update'
                    ],
                    [
                        'title'                         => 'Item Delete',
                        'route'                         => 'admin.setup.sections.item.delete'
                    ],

                ],
            ],
            [
                'title'                                 => 'Setup Pages',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.setup.pages.index'
                    ],
                    [
                        'title'                         => 'Details',
                        'route'                         => 'admin.setup.pages.details'
                    ],
                    [
                        'title'                         => 'Update Sections',
                        'route'                         => 'admin.setup.pages.update.section'
                    ],
                    [
                        'title'                         => 'Status Update',
                        'route'                         => 'admin.setup.pages.status.update'
                    ],
                ],
            ],
            [
                'title'                                 => 'Language',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.languages.index'
                    ],
                    [
                        'title'                         => 'Store',
                        'route'                         => 'admin.languages.store'
                    ],
                    [
                        'title'                         => 'Update',
                        'route'                         => 'admin.languages.update'
                    ],
                    [
                        'title'                         => 'Status Update',
                        'route'                         => 'admin.languages.status.update'
                    ],
                    [
                        'title'                         => 'Info',
                        'route'                         => 'admin.languages.info'
                    ],
                    [
                        'title'                         => 'Import',
                        'route'                         => 'admin.languages.import'
                    ],
                    [
                        'title'                         => 'Delete',
                        'route'                         => 'admin.languages.delete'
                    ],
                    [
                        'title'                         => 'Switch',
                        'route'                         => 'admin.languages.switch'
                    ],
                    [
                        'title'                         => 'Download',
                        'route'                         => 'admin.languages.download'
                    ],
                ],
            ],
            [
                'title'                                 => 'Extensions',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.extensions.index'
                    ],
                    [
                        'title'                         => 'Update',
                        'route'                         => 'admin.extensions.update'
                    ],
                    [
                        'title'                         => 'Status Update',
                        'route'                         => 'admin.extensions.status.update'
                    ],
                ],
            ],
            [
                'title'                                 => 'Push Notification',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.push.notification.index'
                    ],
                    [
                        'title'                         => 'Config',
                        'route'                         => 'admin.push.notification.config'
                    ],
                    [
                        'title'                         => 'Update',
                        'route'                         => 'admin.push.notification.update'
                    ],
                    [
                        'title'                         => 'Send',
                        'route'                         => 'admin.push.notification.send'
                    ],
                    [
                        'title'                         => 'Broadcast Update',
                        'route'                         => 'admin.push.notification.broadcast.config.update'
                    ],
                ],
            ],
            [
                'title'                                 => 'Useful Links',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.useful.links.index'
                    ],
                    [
                        'title'                         => 'Store',
                        'route'                         => 'admin.useful.links.store'
                    ],
                    [
                        'title'                         => 'Status Update',
                        'route'                         => 'admin.useful.links.status.update'
                    ],
                    [
                        'title'                         => 'Edit',
                        'route'                         => 'admin.useful.links.edit'
                    ],
                    [
                        'title'                         => 'Update',
                        'route'                         => 'admin.useful.links.update'
                    ],
                    [
                        'title'                         => 'Delete',
                        'route'                         => 'admin.useful.links.delete'
                    ],
                ],
            ],
        ],
    ],
    // 'transaction-log-permission'                                           => [
    //     'title'                                         => 'Transaction Logs Permissions',
    //     'sections'                                      =>  [
    //         [
    //             'title'                                 => 'Transfer Money Logs',
    //             'routes'                                => [
    //                 [
    //                     'title'                         => 'Index',
    //                     'route'                         => 'admin.transfer.money.index'
    //                 ],
    //                 [
    //                     'title'                         => 'Pending',
    //                     'route'                         => 'admin.transfer.money.pending'
    //                 ],
    //                 [
    //                     'title'                         => 'Payment Success',
    //                     'route'                         => 'admin.transfer.money.payment.success'
    //                 ],
    //                 [
    //                     'title'                         => 'Canceled',
    //                     'route'                         => 'admin.transfer.money.canceled'
    //                 ],
    //                 [
    //                     'title'                         => 'Transfer Success',
    //                     'route'                         => 'admin.transfer.money.transfer.success'
    //                 ],
    //                 [
    //                     'title'                         => 'Transfer Details',
    //                     'route'                         => 'admin.transfer.money.Details'
    //                 ],
    //                 [
    //                     'title'                         => 'Approve Transfer',
    //                     'route'                         => 'admin.transfer.money.approve'
    //                 ],
    //                 [
    //                     'title'                         => 'Reject Transfer',
    //                     'route'                         => 'admin.transfer.money.reject'
    //                 ],
    //                 [
    //                     'title'                         => 'Make Success Transfer',
    //                     'route'                         => 'admin.transfer.money.transaction.success'
    //                 ],
    //                 [
    //                     'title'                         => 'Search Transfer',
    //                     'route'                         => 'admin.transfer.money.search'
    //                 ],
    //             ],
    //         ],

    //     ],
    // ],
    'support-permission'                                           => [
        'title'                                         => 'Support Permissions',
        'sections'                                      =>  [
            [
                'title'                                 => 'Support Ticket',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.support.ticket.index'
                    ],
                    [
                        'title'                         => 'Active',
                        'route'                         => 'admin.support.ticket.active'
                    ],
                    [
                        'title'                         => 'Pending',
                        'route'                         => 'admin.support.ticket.pending'
                    ],
                    [
                        'title'                         => 'Solved',
                        'route'                         => 'admin.support.ticket.solved'
                    ],
                    [
                        'title'                         => 'Conversation',
                        'route'                         => 'admin.support.ticket.conversation'
                    ],
                    [
                        'title'                         => 'Reply Message',
                        'route'                         => 'admin.support.ticket.messaage.reply'
                    ],
                    [
                        'title'                         => 'Solve',
                        'route'                         => 'admin.support.ticket.solve'
                    ],
                    [
                        'title'                         => 'Create',
                        'route'                         => 'admin.support.ticket.create'
                    ],
                    [
                        'title'                         => 'Store',
                        'route'                         => 'admin.support.ticket.store'
                    ],
                    [
                        'title'                         => 'Bulk Delete',
                        'route'                         => 'admin.support.ticket.bulk.delete'
                    ],
                    [
                        'title'                         => 'Delete',
                        'route'                         => 'admin.support.ticket.delete'
                    ],
                ],
            ],
            [
                'title'                                 => 'Contact Message',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.contact.messages.index'
                    ],
                    [
                        'title'                         => 'Reply',
                        'route'                         => 'admin.contact.messages.reply'
                    ],

                ],
            ],
            [
                'title'                                 => 'Subscriber',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.subscriber.index'
                    ],
                    [
                        'title'                         => 'Send Mail',
                        'route'                         => 'admin.subscriber.send.mail'
                    ],

                ],
            ],
        ],
    ],
    'bonus-permission'                                  => [
        'title'                                         => 'Bonus Permissions',
        'sections'                                      =>  [
            [
                'title'                                 => 'Server Info',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.server.info.index'
                    ],
                ],
            ],
            [
                'title'                                 => 'Error Logs',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.error.logs.index'
                    ]
                ],
            ],
            [
                'title'                                 => 'Cache',
                'routes'                                => [
                    [
                        'title'                         => 'Cache',
                        'route'                         => 'admin.cache.clear'
                    ],
                ],
            ],
            [
                'title'                                 => 'GDPR Cookie',
                'routes'                                => [
                    [
                        'title'                         => 'Index',
                        'route'                         => 'admin.cookie.clear'
                    ],
                    [
                        'title'                         => 'Update',
                        'route'                         => 'admin.cookie.update'
                    ],
                ],
            ],

        ],
    ],
];
