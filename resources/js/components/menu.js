export const menuItems = [
    {
        id: 1,
        label: "menuitems.navigation.text",
        isTitle: true
    },
    {
        id: 100032,
        label: 'menuitems.company.text',
        icon: 'fas fa-city',
        name: 'company',
        link: '/dashboard/company'
    },
    {
        id: 10007,
        label: 'menuitems.branch.text',
        icon: 'fas fa-code-branch',
        name: 'branch',
        link: '/dashboard/branch'
    },
    {
        id: 10008,
        label: 'menuitems.store.text',
        name: 'store',
        icon: 'fas fa-store',
        link: '/dashboard/store'
    },
    {
        id: 10009,
        label: 'menuitems.serial.text',
        name: 'serial',
        icon: 'fas fa-eraser',
        link: '/dashboard/serial'
    },
    {
        id: 113872,
        label: "general.Properties",
        icon: "fas fa-hand-spock",
        name: 'properties',
        isMenuCollapsed: false,
        subItems: [
            {
                id: 225,
                label: 'general.ScreenProperties',
                name: 'screen properties',
                link: '/dashboard/screen-properties'
            },
            {
                id: 226,
                label: 'general.TreeProperty',
                name: 'tree property',
                link: '/dashboard/tree-properties'
            },
        ]
    },
    {
        id: 10006,
        label: "menuitems.salesMen.text",
        icon: "fas fa-user-tag",
        name: 'sales men',
        isMenuCollapsed: false,
        subItems: [
            {
                id: 10027,
                label: 'menuitems.dashboard.list.salesMenType',
                name: 'sales men Type',
                link: '/dashboard/salesmenTypes'
            },
            {
                id: 100117,
                label: 'menuitems.dashboard.list.salesMen',
                name: 'sales men',
                link: '/dashboard/salesmen'
            },
            {
                id: 7636473,
                label: 'menuitems.dashboard.list.internalSalesmen',
                name: 'internal salesmen',
                link: '/dashboard/internalSalesman'
            },
        ]
    },
    {
        id: 1000548,
        label: "menuitems.Banks.text",
        icon: "fas fa-piggy-bank",
        name: 'bank',
        isMenuCollapsed: false,
        subItems: [
            {
                id: 112201,
                label: 'general.paymentTypes',
                name: 'payment types',
                link: '/dashboard/paymentTypes'
            },
            {
                id: 222,
                label: 'menuitems.Banks.text',
                name: 'banks',
                link: '/dashboard/banks'
            },
            {
                id: 112202,
                label: 'general.bankAccounts',
                name: 'bank accounts',
                link: '/dashboard/bankAccount'
            }
        ]
    },
    {
        id: 1000544,
        label: "menuitems.area.text",
        icon: "fas fa-flag",
        name: "area",
        isMenuCollapsed: false,
        subItems: [
            {
                id: 2,
                label: 'menuitems.dashboard.list.country',
                name: "country",
                link: '/dashboard/country'
            },
            {
                id: 3,
                label: 'menuitems.dashboard.list.governorate',
                name: "governorate",
                link: '/dashboard/governorate'

            },
            {
                id: 4,
                label: 'menuitems.dashboard.list.city',
                name: "city",
                link: '/dashboard/city'
            },
            {
                id: 8,
                label: 'menuitems.dashboard.list.avenue',
                name: "avenue",
                link: '/dashboard/avenue'
            },
            {
                id: 8,
                label: 'general.street',
                name: "street",
                link: '/dashboard/street'
            },
        ]
    },
    {
        id: 10001,
        label: "menuitems.role.text",
        icon: "ri-shield-user-line",
        name: 'role',
        isMenuCollapsed: false,
        subItems: [
            {
                id: 10002,
                name: 'role Type',
                label: 'menuitems.dashboard.list.rolesType',
                link: '/dashboard/rolesType'
            },

            {
                id: 10003,
                name: 'roles',
                label: 'menuitems.dashboard.list.roles',
                link: '/dashboard/roles'
            },
            {
                id: 10003,
                name: 'user role',
                label: 'general.userRole',
                link: '/dashboard/user-role'
            },
            {
                id: 100043,
                name: 'role workflow',
                label: 'menuitems.dashboard.list.roleWorkflow',
                link: '/dashboard/role-workflow'
            },
            // {
            //     id: 100088,
            //     name: 'role workflow button',
            //     label: 'menuitems.dashboard.list.roleWorkflowButton',
            //     link: '/dashboard/role-workflow-button'
            // },
            // {
            //     id: 100134,
            //     name: 'role hotfield screen',
            //     label: 'menuitems.dashboard.list.RoleHotfieldScreen',
            //     link: '/dashboard/role-hotfield-screen'
            // },
        ]
    },
    {
        id: 1112568,
        label: "general.club",
        icon: "fas fa-hotel",
        name: 'club',
        isMenuCollapsed: false,
        subItems: [
            {
                id: 100211,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.lookup',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 12223,
                        name: 'club-sponsor',
                        label: 'general.sponsor',
                        link: '/dashboard/club/sponsor'
                    },
                    {
                        id: 122155,
                        name: 'club-setting',
                        label: 'general.settings',
                        link: '/dashboard/club/settings'
                    },
                    // {
                    //     id: 13033,
                    //     name: 'club-pending-member',
                    //     label: 'general.pendingMembers',
                    //     link: '/dashboard/club/pending-member'
                    // },
                    {
                        id: 130303,
                        name: 'club-financial-status',
                        label: 'general.financial_status',
                        link: '/dashboard/club/financial-status'
                    },
                ]
            },
            {
                id: 134211,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.master_file',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 12221,
                        name: 'club-member-apply',
                        label: 'general.memberRequest',
                        link: '/dashboard/club/member-apply'
                    },
                    {
                        id: 12214,
                        name: 'club-member',
                        label: 'general.member',
                        link: '/dashboard/club/member'
                    },
                    {
                        id: 12210,
                        name: 'club-member-Request',
                        label: 'general.accept-reject-member',
                        link: '/dashboard/club/members-requests'
                    },
                    {
                        id: 12222,
                        name: 'club-member-accept',
                        label: 'general.memberAccept',
                        link: '/dashboard/club/member-accept'
                    },
                    {
                        id: 12214,
                        name: 'club-membership-renewal',
                        label: 'general.membership_renewal',
                        link: '/dashboard/club/membership-renewal'
                    }
                ]
            },
            {
                id: 134211,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.transaction',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 130366,
                        name: 'club-subscription',
                        label: 'general.subscription',
                        link: '/dashboard/club/subscription'
                    },
                    {
                        id: 13035656,
                        name: 'club-multi-subscription',
                        label: 'general.multiSubscription',
                        link: '/dashboard/club/multiSubscription'
                    },
                    {
                        id: 13036996,
                        name: 'club-change-spenser',
                        label: 'general.changeSpenser',
                        link: '/dashboard/club/change-spenser'
                    },
                ]
            },
            {
                id: 1021111,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.tools',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 100222,
                        label: 'general.customTable',
                        icon: 'fas fa-tasks',
                        name: 'custom table',
                        link: '/dashboard/custom-table'
                    },
                    {
                        id: 100004,
                        name: 'real estate',
                        label: 'general.DatabaseBackup',
                        icon: 'far fa-list-alt',
                        link: '/dashboard/database-backup'
                    },
                ]
            },
            {
                id: 300201,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.reports',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 300201,
                        name: 'real estate',
                        label: 'general.PayerMemberReport',
                        link: '/dashboard/club/payer-member-report'
                    },
                    {
                        id: 313301,
                        name: 'real estate',
                        label: 'general.PermissionMemberReport',
                        link: '/dashboard/club/permission-member-report'
                    },
                    {
                        id: 313399601,
                        name: 'real estate',
                        label: 'general.UncoverReports',
                        link: '/'
                    },
                    {
                        id: 318745601,
                        name: 'real estate',
                        label: 'general.ElectionDisclosure',
                        link: '/'
                    },
                    {
                        id: 3982639601,
                        name: 'real estate',
                        label: 'general.PaymentsAndSubscriptions',
                        link: '/'
                    },
                ]
            },
        ]
    },
    {
        id: 1112568,
        label: "general.hr",
        icon: "fas fa-hotel",
        name: 'hr',
        isMenuCollapsed: false,
        subItems: [
            {
                id: 12222,
                name: 'hr',
                label: 'general.payrollHeads',
                link: '/dashboard/hr-payroll-head'
            },
            {
                id: 12223,
                name: 'hr',
                label: 'general.jobTitles',
                link: '/dashboard/hr-job-title'
            },
            {
                id: 12224,
                name: 'hr',
                label: 'general.requestTypes',
                link: '/dashboard/hr-request-type'
            },
            {
                id: 123334,
                name: 'hr',
                label: 'general.onlineRequests',
                link: '/dashboard/hr-online-request'
            },
        ]
    },
    {
        id: 10024568,
        label: "general.realEstate",
        icon: "fas fa-hotel",
        name: 'real estate',
        isMenuCollapsed: false,
        subItems: [
            {
                id: 100211,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.lookup',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 102221,
                        name: 'realEstate-category',
                        label: 'general.category',
                        link: '/dashboard/realEstate/category'
                    },
                    {
                        id: 103331,
                        name: 'realEstate-item',
                        label: 'general.Items',
                        link: '/dashboard/realEstate/item'
                    },
                    {
                        id: 100001,
                        name: 'realEstate unit status',
                        label: 'general.unitstatus',
                        link: '/dashboard/realEstate/unitstatus'
                    },
                    {
                        id: 1022323,
                        name: 'wallets',
                        label: 'general.wallet',
                        link: '/dashboard/realEstate/wallet'
                    },
                    {
                        id: 1000201,
                        name: 'owners',
                        label: 'general.owner',
                        link: '/dashboard/realEstate/owner'
                    },
                    // {
                    //     id: 1042323,
                    //     name: 'wallet owner',
                    //     label: 'general.walletOwner',
                    //     link: '/dashboard/realEstate/wallet-owner'
                    // },
                ]
            },
            {
                id: 134211,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.master_file',
                isMenuCollapsed: false,
                subItems: [
                    // {
                    //     id: 102371,
                    //     name: 'building wallet',
                    //     label: 'general.building_wallet',
                    //     link: '/dashboard/realEstate/building-wallet'
                    // },
                    {
                        id: 10048103,
                        name: 'building',
                        label: 'general.building',
                        link: '/dashboard/realEstate/building'
                    },
                    {
                        id: 1042323,
                        name: 'realEstate units',
                        label: 'units.units',
                        link: '/dashboard/realEstate/unit'
                    }
                ]
            },
            {
                id: 134211,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.transaction',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 1042323,
                        name: 'reservation',
                        label: 'general.reservation',
                        link: '/dashboard/realEstate/reservation'
                    },
                    {
                        id: 100111,
                        name: 'contract',
                        label: 'general.contract',
                        link: '/dashboard/realEstate/contract'
                    },
                    {
                        id: 100221,
                        name: 'invoice',
                        label: 'general.Invoice',
                        link: '/dashboard/realEstate/invoice'
                    },
                    // {
                    //     id: 100021,
                    //     name: 'contract unit',
                    //     label: 'general.invoice',
                    //     link: '/dashboard/realEstate/contractunit'
                    // }
                ]
            },
            {
                id: 333201,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.reports',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 333200,
                        name: 'real estate',
                        label: 'general.Finance report',
                        link: '/'
                    },
                    {
                        id: 333201,
                        name: 'real estate',
                        label: 'general.Admin report',
                        isMenuCollapsed: false,
                        subItems: [
                            {
                                id: 322201,
                                name: 'real estate',
                                label: 'general.SolidUnitReport',
                                link: '/dashboard/realEstate/admin-report',
                            },
                            {
                                id: 322201,
                                name: 'real estate',
                                label: 'general.UnsolidUnitReport',
                                link: '/dashboard/realEstate/unsold-unit-report',
                            }
                        ]
                    },
                ]
            },
            {
                id: 1021111,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.tools',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 100222,
                        label: 'general.customTable',
                        icon: 'fas fa-tasks',
                        name: 'custom table',
                        link: '/dashboard/custom-table'
                    },
                    {
                        id: 100004,
                        name: 'real estate',
                        label: 'general.DatabaseBackup',
                        icon: 'far fa-list-alt',
                        link: '/dashboard/database-backup'
                    },
                ]
            },
        ]
    },
    {
        id: 1002168,
        label: "general.boardRent",
        icon: "fas fa-hotel",
        name: 'board rent',
        isMenuCollapsed: false,
        subItems: [
            {
                id: 10211,
                name: 'board rent',
                label: 'general.master_file',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 100021,
                        name: 'board-realEstate unit status',
                        label: 'general.panel',
                        link: '/dashboard/boardRent/panel'
                    },
                    {
                        id: 100022,
                        name: 'board-realEstate unit status',
                        label: 'general.package',
                        link: '/dashboard/boardRent/package'
                    },
                ]
            },
            {
                id: 14211,
                name: 'real estate',
                label: 'general.transaction',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 102375,
                        name: 'building wallet',
                        label: 'general.tasks',
                        link: '/dashboard/boardRent/tasks'
                    },
                    {
                        id: 1023775,
                        name: 'boardRent tasks calender',
                        label: 'general.TasksCalender',
                        link: '/dashboard/boardRent/tasks-calender'
                    },
                    {
                        id: 102373,
                        name: 'building wallet',
                        label: 'general.quotation',
                        link: '/dashboard/boardRent/quotation'
                    },
                    {
                        id: 102372,
                        name: 'building wallet',
                        label: 'general.order',
                        link: '/dashboard/boardRent/order'
                    },
                    {
                        id: 100002373,
                        name: 'board-Rent-quotation',
                        label: 'general.quotation',
                        link: '/dashboard/boardRent/board-Rent-quotation'
                    },
                    {
                        id: 102374,
                        name: 'building wallet',
                        label: 'general.invoice',
                        link: '/dashboard/boardRent/invoice'
                    }
                ]
            },
            {
                id: 14211,
                name: 'real estate',
                label: 'general.systemSetup',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 102175,
                        name: 'building wallet',
                        label: 'general.sector',
                        link: '/dashboard/boardRent/sector'
                    },
                    {
                        id: 102276,
                        name: 'building wallet',
                        label: 'general.unitStatus',
                        link: '/dashboard/boardRent/unit-status'
                    },
                    {
                        id: 102277,
                        name: 'building wallet',
                        label: 'general.lookup',
                        link: '/dashboard/boardRent/look-up'
                    },
                ]
            },
            {
                id: 1021111,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.tools',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 100004,
                        name: 'real estate',
                        label: 'general.DatabaseBackup',
                        icon: 'far fa-list-alt',
                        link: '/dashboard/database-backup'
                    },
                    {
                        id: 1222,
                        label: 'general.customTable',
                        icon: 'fas fa-tasks',
                        name: 'custom table',
                        link: '/dashboard/custom-table'
                    },
                ]
            },
        ]
    },
    {
        id: 1004346756,
        label: "general.archive",
        name: 'archiving',
        icon: "ri-share-line",
        isMenuCollapsed: false,
        subItems: [
            {
                id: 432234,
                name: 'gen arch doc types',
                label: 'menuitems.GenArchDocType.text',
                link: '/dashboard/documents'
            },
            {
                id: 38781,
                name: 'document field',
                label: 'menuitems.DocumentField.text',
                link: '/dashboard/document-fields'
            },
            {
                id: 879756,
                name: 'arch departments',
                label: 'menuitems.ArchDepartment.text',
                link: '/dashboard/arch-departments'
            },
            // {
            //     id: 1567443,
            //     name: 'arch documents',
            //     label: 'general.arch_document',
            //     link: '/dashboard/arch-documents'
            // },
            {
                id: 32020,
                name: 'arch doc status',
                label: 'menuitems.ArchDocumentStatus.text',
                link: '/dashboard/arch-doc-status'
            },
            {
                id: 3210181,
                name: 'archiving screen',
                label: 'general.archiving',
                link: '/dashboard/archiving'
            },
            // {
            //     id: 34561,
            //     name: 'arch doc type fields',
            //     label: 'menuitems.ArchDocTypeField.text',
            //     link: '/dashboard/arch-doc-type-fields'
            // },
            // {
            //     id: 36462,
            //     name: 'arch document dtls',
            //     label: 'menuitems.ArchDocumentDtl.text',
            //     link: '/dashboard/arch-document-dtls'
            // },
            // {
            //     id: 34343,
            //     name: 'archive closed references',
            //     label: 'menuitems.ArchiveClosedReference.text',
            //     link: '/dashboard/archive-closed-references'
            // }
        ]
    },
    {
        id: 1004325156,
        label: "general.recievablePayable",
        icon: "fas fa-money-check-alt",
        name: 'receivable payable',
        isMenuCollapsed: false,
        subItems: [
            {
                id: 103311,
                icon: "fas fa-hotel",
                name: 'receivable payable',
                label: 'general.lookup',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 387181,
                        name: 'installment status',
                        label: 'general.installmentStatus',
                        link: '/dashboard/installment-status'
                    },
                    {
                        id: 3874261,
                        name: 'installment payment type',
                        label: 'general.installmentPaymentType',
                        link: '/dashboard/installment-payment-type',
                    },
                    // {
                    //     id: 387192,
                    //     name: 'main contact groups',
                    //     label: 'general.MainContactGroups',
                    //     link: '/dashboard/MainContactGroups'
                    // },
                    // {
                    //     id: 387192,
                    //     name: 'subContactGroup',
                    //     label: 'general.subcontactgroup',
                    //     link: '/dashboard/sub-contact-group'
                    // },
                ]
            },
            {
                id: 103311,
                icon: "fas fa-hotel",
                name: 'receivable payable',
                label: 'general.master_file',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 3243193,
                        name: 'installment payment plan',
                        label: 'general.installmentPaymentPlan',
                        link: '/dashboard/installment-payment-plan'
                    },
                    // {
                    //     id: 387193,
                    //     name: 'installment payment plan detail',
                    //     label: 'general.InstallmentPaymentPlanDetail',
                    //     link: '/dashboard/InstallmentPaymentPlanDetail'
                    // },
                    {
                        id: 1222,
                        label: 'general.DocumentPlan',
                        icon: 'fas fa-code-branch',
                        name: 'installment document plan',
                        link: '/dashboard/installment-document-plan'
                    },
                    // {
                    //     id: 1222,
                    //     label: 'general.ScreenSubcontactGroup',
                    //     name: 'screen subcontact group',
                    //     link: '/dashboard/screen-subcontact-group'
                    // },
                    {
                        id: 387171,
                        name: 'installment condition',
                        label: 'general.installmentCondition',
                        link: '/dashboard/installment-condition'
                    },

                    {
                        id: 1222,
                        label: 'general.payemntPlanInstallment',
                        icon: 'fas fa-code-branch',
                        name: 'payment plan installment',
                        link: '/dashboard/payment-plan-installments'
                    },
                ]
            },
            {
                id: 123311,
                icon: "fas fa-hotel",
                name: 'receivable payable',
                label: 'general.transaction',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 2241,
                        label: 'general.openinig balance',
                        icon: 'fas fa-hot-tub',
                        name: 'receivable payable',
                        link: '/dashboard/receivablePayable/openingBalance'
                    },
                    {
                        id: 103231,
                        name: 'reservation',
                        label: 'general.reservation',
                        link: '/dashboard/receivablePayable/reservation'
                    },
                    {
                        id: 11111,
                        name: 'contract',
                        label: 'general.contract',
                        link: '/dashboard/receivablePayable/contract'
                    },
                    {
                        id: 11034,
                        label: 'general.reciept vourcher',
                        icon: 'fas fa-hot-tub',
                        name: 'receipt voucher',
                        link: '/dashboard/receivablePayable/voucher'
                    },
                    {
                        id: 13334,
                        label: 'general.credit note',
                        icon: 'fas fa-hot-tub',
                        name: 'credit note',
                        link: '/dashboard/receivablePayable/creditNote'
                    },
                    {
                        id: 13334,
                        label: 'general.debit note',
                        icon: 'fas fa-hot-tub',
                        name: 'debit note',
                        link: '/dashboard/receivablePayable/debitNote'
                    },
                    {
                        id: 13334,
                        label: 'general.issue vourcher',
                        icon: 'fas fa-hot-tub',
                        name: 'receivable payable',
                        link: '/'
                    },
                ]
            },
            {
                id: 1021111,
                icon: "fas fa-hotel",
                name: 'receivable payable',
                label: 'general.reports',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 1003321,
                        name: 'receivable payable',
                        label: 'general.Finance report',
                        isMenuCollapsed: false,
                        subItems: [
                            {
                                id: 9876542859,
                                label: 'general.PaymentsReport',
                                icon: 'fas fa-hot-tub',
                                name: 'receivable payable',
                                link: '/dashboard/receivablePayable/report/financeReports/payments'
                            },
                            {
                                id: 6985623,
                                label: 'general.VoucherReport',
                                icon: 'fas fa-hot-tub',
                                name: 'receivable payable',
                                link: '/dashboard/receivablePayable/report/financeReports/voucher'
                            },
                        ]
                    },
                    {
                        id: 222311,
                        name: 'receivable payable',
                        label: 'general.Admin report',
                        link: '/'
                    },
                ]
            },
            {
                id: 1021111,
                icon: "fas fa-hotel",
                name: 'real estate',
                label: 'general.tools',
                isMenuCollapsed: false,
                subItems: [
                    {
                        id: 1222,
                        label: 'general.customTable',
                        icon: 'fas fa-tasks',
                        name: 'custom table',
                        link: '/dashboard/custom-table'
                    },
                    {
                        id: 100004,
                        name: 'real estate',
                        label: 'general.DatabaseBackup',
                        icon: 'far fa-list-alt',
                        link: '/dashboard/database-backup'
                    },
                ]
            },
        ]
    },
    {
        id: 224,
        label: 'general.Workflowhotfields',
        icon: 'fas fa-hot-tub',
        name: 'Workflow hot fields',
        link: '/dashboard/workflow-hotfields'
    },
    {
        id: 100023,
        icon: 'fas fa-hot-tub',
        name: 'department',
        label: 'general.department',
        link: '/dashboard/department'
    },
    {
        id: 100123,
        icon: 'fas fa-hot-tub',
        name: 'statuses',
        label: 'general.statuses',
        link: '/dashboard/statuses'
    },
    {
        id: 100124,
        icon: 'fas fa-hot-tub',
        name: 'color',
        label: 'general.category',
        link: '/dashboard/category'
    },
    {
        id: 223,
        name: 'users',
        label: 'general.Users',
        icon: 'fas fa-user',
        link: '/dashboard/users'
    },
    {
        id: 2224,
        name: 'general customer',
        label: 'general.customer',
        icon: 'fas fa-user-friends',
        link: '/dashboard/customer'
    },
    {
        id: 2225,
        name: 'equipment',
        label: 'general.equipments',
        icon: 'fas fa-wrench',
        link: '/dashboard/equipment'
    },
    {
        id: 5,
        name: 'currencies',
        label: 'menuitems.currency.text',
        icon: ' fas fa-dollar-sign',
        link: '/dashboard/currency'
    },
    {
        id: 6,
        name: 'employees',
        label: 'menuitems.employee.text',
        icon: 'fas fa-briefcase',
        link: '/dashboard/employee',
        isMenuCollapsed: false,
        subItems: [
            {
                id: 63332,
                name: 'employees',
                label: 'menuitems.employee.text',
                icon: 'fas fa-briefcase',
                link: '/dashboard/employee',
            },
            {
                id: 7636473,
                label: 'general.plans',
                name: 'employees',
                link: '/dashboard/salesman-plan'
            },
            {
                id: 7636473,
                label: 'general.plandDetails',
                name: 'employees',
                link: '/dashboard/salesman-plan-details'
            },
            {
                id: 9,
                label: 'menuitems.dashboard.list.externalSalesmen',
                name: 'employees',
                link: '/dashboard/externalSalesmen'
            },
        ]
    },
    {
        id: 100024,
        icon: 'fas fa-briefcase',
        name: 'sell method',
        label: 'general.sellMethod',
        link: '/dashboard/boardRent/sell-method'
    },
    {
        id: 7,
        name: 'financial Year',
        label: 'menuitems.financialYear.text',
        icon: 'fas fa-file-invoice-dollar',
        link: '/dashboard/financialYear'
    },
    {
        id: 10004,
        name: 'units',
        label: 'menuitems.units.text',
        icon: 'far fa-list-alt',
        link: '/dashboard/units'
    },
    {
        id: 100006,
        label: "general.document",
        icon: "fas fa-file-alt",
        name: 'document',
        isMenuCollapsed: false,
        subItems: [
            {
                id: 1000627,
                label: 'general.document',
                name: 'document',
                link: '/dashboard/document/index'
            },
            {
                id: 1000627,
                label: 'general.documentStatus',
                name: 'document status',
                link: '/dashboard/document/document-status'
            },
            // {
            //     id: 1000628,
            //     label: 'general.documentReason',
            //     name: 'document reason',
            //     link: '/dashboard/document/document-reason'
            // },

        ]
    },
    {
        id: 10005,
        name: 'color',
        label: 'menuitems.colors.text',
        icon: 'fas fa-palette',
        link: '/dashboard/colors'
    },

    // {
    //     id: 1115,
    //     label: "menuitems.dashboard.text",
    //     icon: "ri-dashboard-line",
    //     badge: {
    //         variant: "success",
    //         text: "menuitems.dashboard.badge"
    //     },
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1114,
    //             label: 'menuitems.dashboard.list.sales',
    //             link: '/dashboard/'
    //         },
    //         {
    //             id: 1113,
    //             label: 'menuitems.dashboard.list.crm',
    //             link: '/dashboard/dashboard/crm'
    //         },
    //         {
    //             id: 1115,
    //             label: 'menuitems.dashboard.list.analytics',
    //             link: '/dashboard/dashboard/analytics'
    //         },
    //     ]
    // },
    // {
    //     id: 1114,
    //     label: "menuitems.apps.text",
    //     isTitle: true
    // },
    // {
    //     id: 11113,
    //     label: "menuitems.chat.text",
    //     icon: "ri-message-2-line",
    //     link: '/dashboard/apps/chat'
    // },
    // {
    //     id: 1112,
    //     label: "menuitems.ecommerce.text",
    //     icon: "ri-shopping-cart-2-line",
    //     badge: {
    //         variant: "danger",
    //         text: "menuitems.ecommerce.badge"
    //     },
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1111,
    //             label: "menuitems.ecommerce.list.products",
    //             link: "/dashboard/ecommerce/products"
    //         },
    //         {
    //             id: 1110,
    //             label: "menuitems.ecommerce.list.productsgrid",
    //             link: "/dashboard/ecommerce/products-grid"
    //         },
    //         {
    //             id: 1109,
    //             label: 'menuitems.ecommerce.list.productdetail',
    //             link: '/dashboard/ecommerce/product-detail/1'
    //         },
    //         {
    //             id: 1108,
    //             label: 'menuitems.ecommerce.list.createproduct',
    //             link: '/dashboard/ecommerce/product-create'
    //         },
    //         {
    //             id: 1107,
    //             label: "menuitems.ecommerce.list.customers",
    //             link: "/dashboard/ecommerce/customers"
    //         },
    //         {
    //             id: 1106,
    //             label: "menuitems.ecommerce.list.orders",
    //             link: "/dashboard/ecommerce/orders"
    //         },
    //         {
    //             id: 1105,
    //             label: "menuitems.ecommerce.list.orderdetail",
    //             link: "/dashboard/ecommerce/order-detail"
    //         },
    //         {
    //             id: 1104,
    //             label: "menuitems.ecommerce.list.sellers",
    //             link: "/dashboard/ecommerce/sellers"
    //         },
    //         {
    //             id: 1103,
    //             label: "menuitems.ecommerce.list.cart",
    //             link: "/dashboard/ecommerce/cart"
    //         },
    //         {
    //             id: 1102,
    //             label: "menuitems.ecommerce.list.checkout",
    //             link: "/dashboard/ecommerce/checkout"
    //         }
    //     ]
    // },
    // {
    //     id: 1101,
    //     label: 'menuitems.calendar.text',
    //     icon: 'ri-calendar-2-line',
    //     link: '/dashboard/apps/calendar'
    // },
    // {
    //     id: 1100,
    //     label: 'menuitems.email.text',
    //     icon: 'ri-mail-line',
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1099,
    //             label: 'menuitems.email.list.inbox',
    //             link: '/dashboard/email/inbox'
    //         },
    //         {
    //             id: 1098,
    //             label: 'menuitems.email.list.reademail',
    //             link: '/dashboard/email/reademail/1'
    //         },
    //         {
    //             id: 1097,
    //             label: 'menuitems.email.list.template',
    //             link: '/dashboard/email/templates'
    //         }
    //     ]
    // },
    // {
    //     id: 1096,
    //     label: 'menuitems.companies.text',
    //     icon: 'ri-building-4-line',
    //     link: '/dashboard/apps/companies'
    // },
    // {
    //     id: 1095,
    //     label: 'menuitems.tasks.text',
    //     icon: 'ri-task-line',
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1093,
    //             label: 'menuitems.tasks.list.list',
    //             link: '/dashboard/task/list'
    //         },
    //         {
    //             id: 1092,
    //             label: 'menuitems.tasks.list.detail',
    //             link: '/dashboard/task/detail'
    //         },
    //         {
    //             id: 1091,
    //             label: 'menuitems.tasks.list.kanban',
    //             link: '/dashboard/task/kanban'
    //         }
    //     ]
    // },
    // {
    //     id: 1090,
    //     label: 'menuitems.ticket.text',
    //     icon: 'ri-customer-service-2-line',
    //     link: '/dashboard/apps/tickets'
    // },
    // {
    //     id: 1089,
    //     label: 'menuitems.contacts.text',
    //     icon: 'ri-profile-line',
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1088,
    //             label: 'menuitems.contacts.list.members',
    //             link: '/dashboard/contacts/list'
    //         },
    //         {
    //             id: 1087,
    //             label: 'menuitems.contacts.list.profile',
    //             link: '/dashboard/contacts/profile'
    //         }
    //     ]
    // },
    // {
    //     id: 1086,
    //     label: 'menuitems.filemanager.text',
    //     icon: 'ri-folders-line',
    //     link: '/dashboard/apps/file-manager'
    // },
    // {
    //     id: 1085,
    //     label: "menuitems.custom.text",
    //     isTitle: true
    // },
    // {
    //     id: 1084,
    //     label: 'menuitems.auth.text',
    //     icon: 'ri-shield-user-line',
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1083,
    //             label: 'menuitems.auth.list.login',
    //             link: '/dashboard/auth/login-1'
    //         },
    //         {
    //             id: 1082,
    //             label: 'menuitems.auth.list.login-2',
    //             link: '/dashboard/auth/login-2'
    //         },
    //         {
    //             id: 1081,
    //             label: 'menuitems.auth.list.register',
    //             link: '/dashboard/auth/register-1'
    //         },
    //         {
    //             id: 1080,
    //             label: 'menuitems.auth.list.register-2',
    //             link: '/dashboard/auth/register-2'
    //         },
    //         {
    //             id: 1079,
    //             label: 'menuitems.auth.list.signin-signup',
    //             link: '/dashboard/auth/signin-signup'
    //         },
    //         {
    //             id: 1078,
    //             label: 'menuitems.auth.list.signin-signup-2',
    //             link: '/dashboard/auth/signin-signup-2'
    //         },
    //         {
    //             id: 1077,
    //             label: 'menuitems.auth.list.recoverpwd',
    //             link: '/dashboard/auth/recoverpwd'
    //         },
    //         {
    //             id: 1076,
    //             label: 'menuitems.auth.list.recoverpwd-2',
    //             link: '/dashboard/auth/recoverpwd-2'
    //         },
    //         {
    //             id: 1075,
    //             label: 'menuitems.auth.list.lock-screen',
    //             link: '/dashboard/auth/lock-screen'
    //         },
    //         {
    //             id: 1074,
    //             label: 'menuitems.auth.list.lock-screen-2',
    //             link: '/dashboard/auth/lock-screen-2'
    //         },
    //         {
    //             id: 1073,
    //             label: 'menuitems.auth.list.logout',
    //             link: '/dashboard/auth/logout-1'
    //         },
    //         {
    //             id: 1072,
    //             label: 'menuitems.auth.list.logout-2',
    //             link: '/dashboard/auth/logout-2'
    //         },
    //         {
    //             id: 1072,
    //             label: 'menuitems.auth.list.confirm-mail',
    //             link: '/dashboard/auth/confirm-mail'
    //         },
    //         {
    //             id: 1072,
    //             label: 'menuitems.auth.list.confirm-mail-2',
    //             link: '/dashboard/auth/confirm-mail-2'
    //         },
    //     ]
    // },
    // {
    //     id: 1071,
    //     label: 'menuitems.extrapages.text',
    //     icon: 'ri-pages-line',
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1070,
    //             label: 'menuitems.extrapages.list.starter',
    //             link: '/dashboard/extras/starter'
    //         },
    //         {
    //             id: 1069,
    //             label: 'menuitems.extrapages.list.timeline',
    //             link: '/dashboard/extras/timeline'
    //         },
    //         {
    //             id: 1068,
    //             label: 'menuitems.extrapages.list.sitemap',
    //             link: '/dashboard/extras/sitemap'
    //         },
    //         {
    //             id: 1067,
    //             label: 'menuitems.extrapages.list.invoice',
    //             link: '/dashboard/extras/invoice'
    //         },
    //         {
    //             id: 1066,
    //             label: 'menuitems.extrapages.list.faqs',
    //             link: '/dashboard/extras/faqs'
    //         },
    //         {
    //             id: 1065,
    //             label: 'menuitems.extrapages.list.search-results',
    //             link: '/dashboard/extras/search-results'
    //         },
    //         {
    //             id: 1064,
    //             label: 'menuitems.extrapages.list.pricing',
    //             link: '/dashboard/extras/pricing'
    //         },
    //         {
    //             id: 1063,
    //             label: 'menuitems.extrapages.list.maintenance',
    //             link: '/dashboard/extras/maintenance'
    //         },
    //         {
    //             id: 1062,
    //             label: 'menuitems.extrapages.list.comingsoon',
    //             link: '/dashboard/extras/coming-soon'
    //         },
    //         {
    //             id: 1061,
    //             label: 'menuitems.extrapages.list.lightbox',
    //             link: '/dashboard/extras/lightbox'
    //         },
    //         {
    //             id: 1060,
    //             label: 'menuitems.extrapages.list.error404',
    //             link: '/dashboard/error/404'
    //         },
    //         {
    //             id: 1059,
    //             label: 'menuitems.extrapages.list.error404-alt',
    //             link: '/dashboard/error/404-alt'
    //         },
    //         {
    //             id: 1058,
    //             label: 'menuitems.extrapages.list.error500',
    //             link: '/dashboard/error/500'
    //         }
    //     ]
    // },
    // {
    //     id: 1057,
    //     label: "menuitems.components.text",
    //     isTitle: true
    // },
    // {
    //     id: 1056,
    //     label: 'menuitems.ui.text',
    //     icon: 'ri-pencil-ruler-2-line',
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1055,
    //             label: 'menuitems.ui.list.avatars',
    //             link: '/dashboard/ui/avatars'
    //         },
    //         {
    //             id: 1054,
    //             label: 'menuitems.ui.list.buttons',
    //             link: '/dashboard/ui/buttons'
    //         },
    //         {
    //             id: 1053,
    //             label: 'menuitems.ui.list.cards',
    //             link: '/dashboard/ui/cards'
    //         },
    //         {
    //             id: 1052,
    //             label: 'menuitems.ui.list.carousel',
    //             link: '/dashboard/ui/carousel'
    //         },
    //         {
    //             id: 1051,
    //             label: 'menuitems.ui.list.dropdowns',
    //             link: '/dashboard/ui/dropdowns'
    //         },
    //         {
    //             id: 1050,
    //             label: 'menuitems.ui.list.video',
    //             link: '/dashboard/ui/video'
    //         },
    //         {
    //             id: 1049,
    //             label: 'menuitems.ui.list.general',
    //             link: '/dashboard/ui/general'
    //         },
    //         {
    //             id: 1048,
    //             label: 'menuitems.ui.list.grid',
    //             link: '/dashboard/ui/grid'
    //         },
    //         {
    //             id: 1047,
    //             label: 'menuitems.ui.list.images',
    //             link: '/dashboard/ui/images'
    //         },
    //         {
    //             id: 1046,
    //             label: 'menuitems.ui.list.listgroup',
    //             link: '/dashboard/ui/list-group'
    //         },
    //         {
    //             id: 1045,
    //             label: 'menuitems.ui.list.modals',
    //             link: '/dashboard/ui/modals'
    //         },
    //         {
    //             id: 1044,
    //             label: 'menuitems.ui.list.notifications',
    //             link: '/dashboard/ui/notifications'
    //         },
    //         {
    //             id: 1043,
    //             label: 'menuitems.ui.list.portlet',
    //             link: '/dashboard/ui/portlets'
    //         },
    //         {
    //             id: 1042,
    //             label: 'menuitems.ui.list.progress',
    //             link: '/dashboard/ui/progress'
    //         },
    //         {
    //             id: 1041,
    //             label: 'menuitems.ui.list.ribbons',
    //             link: '/dashboard/ui/ribbons'
    //         },
    //         {
    //             id: 1040,
    //             label: 'menuitems.ui.list.spinners',
    //             link: '/dashboard/ui/spinners'
    //         },
    //         {
    //             id: 1039,
    //             label: 'menuitems.ui.list.tabs',
    //             link: '/dashboard/ui/tabs-accordions'
    //         },
    //         {
    //             id: 1038,
    //             label: 'menuitems.ui.list.tooltip',
    //             link: '/dashboard/ui/tooltips-popovers'
    //         },
    //         {
    //             id: 1037,
    //             label: 'menuitems.ui.list.typography',
    //             link: '/dashboard/ui/typography'
    //         }
    //     ]
    // },
    // {
    //     id: 1036,
    //     label: 'menuitems.extendedui.text',
    //     icon: 'ri-stack-line',
    //     badge: {
    //         variant: "primary",
    //         text: "menuitems.extendedui.badge"
    //     },
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1035,
    //             label: 'menuitems.extendedui.list.rangeslider',
    //             link: '/dashboard/extended/rangeslider'
    //         },
    //         {
    //             id: 1034,
    //             label: 'menuitems.extendedui.list.sweetalert',
    //             link: '/dashboard/extended/sweet-alert'
    //         },
    //         {
    //             id: 1033,
    //             label: 'menuitems.extendedui.list.tour',
    //             link: '/dashboard/extended/tour'
    //         },
    //         {
    //             id: 1032,
    //             label: 'menuitems.extendedui.list.scrollspy',
    //             link: '/dashboard/extended/scrollspy'
    //         },
    //     ]
    // },
    // {
    //     id: 1031,
    //     label: 'menuitems.widgets.text',
    //     icon: 'ri-honour-line',
    //     link: '/dashboard/widgets'
    // },
    // {
    //     id: 1024,
    //     label: 'menuitems.icons.text',
    //     icon: 'ri-markup-line',
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1025,
    //             label: 'menuitems.icons.list.feather',
    //             link: '/dashboard/icons/feather'
    //         },
    //         {
    //             id: 1026,
    //             label: 'menuitems.icons.list.remix',
    //             link: '/dashboard/icons/remix'
    //         },
    //         {
    //             id: 1027,
    //             label: 'menuitems.icons.list.boxicons',
    //             link: '/dashboard/icons/boxicons'
    //         },
    //         {
    //             id: 1028,
    //             label: 'menuitems.icons.list.materialdesign',
    //             link: '/dashboard/icons/mdi'
    //         },
    //         {
    //             id: 1029,
    //             label: 'menuitems.icons.list.fontawesome',
    //             link: '/dashboard/icons/font-awesome'
    //         },
    //         {
    //             id: 1030,
    //             label: 'menuitems.icons.list.weather',
    //             link: '/dashboard/icons/weather'
    //         }
    //     ]
    // },
    // {
    //     id: 1023,
    //     label: 'menuitems.forms.text',
    //     icon: 'ri-eraser-line',
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1022,
    //             label: 'menuitems.forms.list.elements',
    //             link: '/dashboard/forms/elements'
    //         },
    //         {
    //             id: 1021,
    //             label: 'menuitems.forms.list.advanced',
    //             link: '/dashboard/forms/advanced'
    //         },
    //         {
    //             id: 1020,
    //             label: 'menuitems.forms.list.validation',
    //             link: '/dashboard/forms/validation'
    //         },
    //         {
    //             id: 1019,
    //             label: 'menuitems.forms.list.wizard',
    //             link: '/dashboard/forms/wizard'
    //         },
    //         {
    //             id: 1018,
    //             label: 'menuitems.forms.list.mask',
    //             link: '/dashboard/forms/mask'
    //         },
    //         {
    //             id: 1017,
    //             label: 'menuitems.forms.list.editor',
    //             link: '/dashboard/forms/quill'
    //         },
    //         {
    //             id: 1016,
    //             label: 'menuitems.forms.list.fileupload',
    //             link: '/dashboard/forms/file-uploads'
    //         }
    //     ]
    // },
    // {
    //     id: 1015,
    //     label: 'menuitems.tables.text',
    //     icon: 'ri-table-line',
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1014,
    //             label: 'menuitems.tables.list.basic',
    //             link: '/dashboard/tables/basic'
    //         },
    //         {
    //             id: 1013,
    //             label: 'menuitems.tables.list.advanced',
    //             link: '/dashboard/tables/advanced'
    //         },
    //     ]
    // },
    // {
    //     id: 1012,
    //     label: 'menuitems.charts.text',
    //     icon: 'ri-bar-chart-line',
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1007,
    //             label: 'menuitems.charts.list.apex',
    //             link: '/dashboard/charts/apex'
    //         },
    //         {
    //             id: 1008,
    //             label: 'menuitems.charts.list.chartjs',
    //             link: '/dashboard/charts/chartjs'
    //         },
    //         {
    //             id: 1009,
    //             label: 'menuitems.charts.list.c3',
    //             link: '/dashboard/charts/c3'
    //         },
    //         {
    //             id: 1010,
    //             label: 'menuitems.charts.list.chartist',
    //             link: '/dashboard/charts/chartist'
    //         },
    //         {
    //             id: 1011,
    //             label: 'menuitems.charts.list.knob',
    //             link: '/dashboard/charts/knob'
    //         }
    //     ]
    // },
    // {
    //     id: 1006,
    //     label: 'menuitems.maps.text',
    //     icon: 'ri-map-pin-line',
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1005,
    //             label: 'menuitems.maps.list.googlemap',
    //             link: '/dashboard/maps/google'
    //         }
    //     ]
    // },
    // {
    //     id: 1004,
    //     label: "menuitems.multilevel.text",
    //     icon: "ri-share-line",
    //     isMenuCollapsed: false,
    //     subItems: [
    //         {
    //             id: 1003,
    //             label: "menuitems.multilevel.list.level1.1",
    //             link: "javascript: void(0);",
    //         },
    //         {
    //             id: 1002,
    //             label: "menuitems.multilevel.list.level1.2",
    //             isMenuCollapsed: false,
    //             subItems: [
    //                 {
    //                     id: 1001,
    //                     label: "menuitems.multilevel.list.level1.level2.1",
    //                     link: "javascript: void(0);"
    //                 },
    //                 {
    //                     id: 1000,
    //                     label: "menuitems.multilevel.list.level1.level2.2",
    //                     link: "javascript: void(0);"
    //                 }
    //             ]
    //         }
    //     ]
    // }
];

