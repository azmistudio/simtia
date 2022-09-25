<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => ['auth']], function() {
    Route::prefix('finance')->group(function() {

        // book year
        Route::group(['prefix' => 'book'], function() {
            // year
            Route::group(['prefix' => 'year'], function() {
                Route::get('/', 'BookYearController@index')->middleware('permission:keuangan-tahun_buku-index');
                Route::post('/store', 'BookYearController@store')->middleware('permission:keuangan-tahun_buku-store');
                Route::get('/show/{id}', 'BookYearController@show');
                Route::get('/active/{id}', 'BookYearController@active');
                Route::get('/period/{id}', 'BookYearController@period');
                Route::post('/data', 'BookYearController@data');
                Route::post('/destroy/{id}', 'BookYearController@destroy')->middleware('permission:keuangan-tahun_buku-destroy');
                Route::post('/export-pdf', 'BookYearController@toPdf');
                Route::post('/combo-grid', 'BookYearController@combogrid');
            });

            // close
            Route::group(['prefix' => 'close'], function() {
                Route::get('/', 'BookYearController@indexClose')->middleware('permission:keuangan-tutup_buku-index');
                Route::post('/store', 'BookYearController@storeClose')->middleware('permission:keuangan-tutup_buku-store');
            });
        });

        // coa
        Route::group(['prefix' => 'coa'], function() {
            Route::get('/', 'COAController@index')->middleware('permission:keuangan-kode_akun_perkiraan-index');
            Route::post('/store', 'COAController@store')->middleware('permission:keuangan-kode_akun_perkiraan-store');
            Route::get('/show/{id}', 'COAController@show');
            Route::get('/data', 'COAController@data');
            Route::post('/data/grid', 'COAController@dataGrid');
            Route::post('/destroy/{id}', 'COAController@destroy')->middleware('permission:keuangan-kode_akun_perkiraan-destroy');
            Route::post('/combo-box/{id}', 'COAController@combobox');
            Route::post('/combo-grid', 'COAController@combogrid');
            Route::post('/export-excel', 'COAController@toExcel');
            Route::post('/export-pdf', 'COAController@toPdf');
            //
            Route::get('/balance', 'COAController@indexBalance');
            Route::post('/balance/store', 'COAController@storeBalance');
        });

        // receipt
        Route::group(['prefix' => 'receipt'], function() {
            Route::get('/', 'ReceiptController@index')->middleware('permission:keuangan-transaksi_penerimaan-index');
            Route::get('/mandatory', 'ReceiptController@indexMandatory');
            Route::get('/voluntary', 'ReceiptController@indexVoluntary');
            Route::get('/other', 'ReceiptController@indexOther');
            Route::post('/store', 'ReceiptController@store')->middleware('permission:keuangan-transaksi_penerimaan-store');
            Route::get('/show/{id}/{category}', 'ReceiptController@show');
            Route::post('/data', 'ReceiptController@data');
            Route::post('/data/voluntary', 'ReceiptController@dataVoluntary');
            Route::post('/data/other', 'ReceiptController@dataOther');
            Route::get('/data/period', 'ReceiptController@dataPeriod');
            Route::get('/data/payment', 'ReceiptController@dataPayment');
            Route::post('/data/print', 'ReceiptController@print');
            Route::post('/data/print/receipt', 'ReceiptController@printReceipt');
            Route::get('/data/show', 'ReceiptController@dataShow');

            // type
            Route::group(['prefix' => 'type'], function() {
                Route::get('/', 'ReceiptTypeController@index')->middleware('permission:keuangan-jenis_penerimaan-index');
                Route::post('/store', 'ReceiptTypeController@store')->middleware('permission:keuangan-jenis_penerimaan-store');
                Route::get('/show/{id}', 'ReceiptTypeController@show');
                Route::post('/data', 'ReceiptTypeController@data');
                Route::post('/destroy/{id}', 'ReceiptTypeController@destroy')->middleware('permission:keuangan-jenis_penerimaan-destroy');
                Route::post('/export-pdf', 'ReceiptTypeController@toPdf');
                Route::post('/combo-box/{id}/{deptid}', 'ReceiptTypeController@combobox');
                Route::post('/combo-grid', 'ReceiptTypeController@combogrid');
                Route::post('/payment/combo-grid', 'ReceiptTypeController@combogridPayment');
            });
            
            // payment
            Route::group(['prefix' => 'payment'], function() {

                // major
                Route::group(['prefix' => 'major'], function() {
                    Route::get('/', 'PaymentMajorController@index')->middleware('permission:keuangan-besar_pembayaran-index');
                    Route::post('/store', 'PaymentMajorController@store')->middleware('permission:keuangan-besar_pembayaran-store');
                    Route::get('/show/{id}', 'PaymentMajorController@show');
                    Route::get('/detail', 'PaymentMajorController@detail');
                    Route::post('/student', 'PaymentMajorController@dataStudent');
                    Route::post('/period/combo-box', 'PaymentMajorController@periodPayment');
                });
            });
        });

        // expenditure
        Route::group(['prefix' => 'expenditure'], function() {
            Route::get('/', 'ExpenditureController@index')->middleware('permission:keuangan-transaksi_pengeluaran-index');
            Route::post('/store', 'ExpenditureController@store')->middleware('permission:keuangan-transaksi_pengeluaran-store');
            Route::get('/show/{id}', 'ExpenditureController@show');
            Route::post('/data', 'ExpenditureController@data');
            Route::post('/data/journal/{id}', 'ExpenditureController@dataJournal');
            Route::post('/data/detail', 'ExpenditureController@dataDetail');
            Route::post('/export-pdf', 'ExpenditureController@toPdf');
            Route::post('/export-excel', 'ExpenditureController@toExcel');
            Route::post('/print/receipt', 'ExpenditureController@printReceipt');

            // requested
            Route::group(['prefix' => 'requested'], function() {
                Route::get('/', 'ExpenditureController@requested');
                Route::post('/data', 'ExpenditureController@dataRequested');
                Route::post('/store', 'ExpenditureController@storeRequested');
                Route::post('/destroy', 'ExpenditureController@destroyRequested');
            });
        });
        
        // saving
        Route::group(['prefix' => 'saving'], function() {
            
            // student
            Route::group(['prefix' => 'student'], function() {

                Route::get('/', 'SavingController@indexStudent')->middleware('permission:keuangan-transaksi_tabungan_santri-index');
                Route::post('/store', 'SavingController@storeStudent')->middleware('permission:keuangan-transaksi_tabungan_santri-store');
                Route::get('/show/{id}', 'SavingController@showStudent');
                Route::get('/info', 'SavingController@infoStudent');
                Route::post('/data', 'SavingController@dataStudent');
                Route::post('/print/pdf', 'SavingController@printPdf');
                Route::post('/print/receipt', 'SavingController@printReceipt');

                // type
                Route::group(['prefix' => 'type'], function() {
                    Route::get('/', 'SavingTypeController@indexStudent')->middleware('permission:keuangan-jenis_tabungan_santri-index');
                    Route::post('/store', 'SavingTypeController@storeStudent')->middleware('permission:keuangan-jenis_tabungan_santri-store');
                    Route::get('/show/{id}', 'SavingTypeController@showStudent');
                    Route::post('/data', 'SavingTypeController@dataStudent');
                    Route::post('/destroy/{id}', 'SavingTypeController@destroyStudent')->middleware('permission:keuangan-jenis_tabungan_santri-destroy');
                    Route::post('/export-pdf', 'SavingTypeController@toPdfStudent');
                    Route::post('/combo-grid', 'SavingTypeController@combogrid');
                    Route::post('/combo-box', 'SavingTypeController@combobox');
                });
            });

            // employee
            Route::group(['prefix' => 'employee'], function() {

                Route::get('/', 'SavingController@indexEmployee')->middleware('permission:keuangan-transaksi_tabungan_pegawai-index');
                Route::post('/store', 'SavingController@storeEmployee')->middleware('permission:keuangan-transaksi_tabungan_pegawai-store');
                Route::get('/show/{id}', 'SavingController@showEmployee');
                Route::get('/info', 'SavingController@infoEmployee');
                Route::post('/data', 'SavingController@dataEmployee');
                Route::post('/print/pdf', 'SavingController@printPdf');
                Route::post('/print/receipt', 'SavingController@printReceipt');

                // type
                Route::group(['prefix' => 'type'], function() {
                    Route::get('/', 'SavingTypeController@indexEmployee')->middleware('permission:keuangan-jenis_tabungan_pegawai-index');
                    Route::post('/store', 'SavingTypeController@storeEmployee')->middleware('permission:keuangan-jenis_tabungan_pegawai-store');
                    Route::get('/show/{id}', 'SavingTypeController@showEmployee');
                    Route::post('/data', 'SavingTypeController@dataEmployee');
                    Route::post('/destroy/{id}', 'SavingTypeController@destroyEmployee')->middleware('permission:keuangan-jenis_tabungan_pegawai-destroy');
                    Route::post('/export-pdf', 'SavingTypeController@toPdfEmployee');
                    Route::post('/combo-grid', 'SavingTypeController@combogrid');
                    Route::post('/combo-box', 'SavingTypeController@combobox');
                });
            });
        });
        
        // journal
        Route::group(['prefix' => 'journal'], function() {
            Route::get('/', 'JournalController@index')->middleware('permission:keuangan-transaksi_jurnal_umum-index');
            Route::post('/store', 'JournalController@store')->middleware('permission:keuangan-transaksi_jurnal_umum-store');
            Route::post('/data', 'JournalController@data');
            Route::post('/data/detail', 'JournalController@dataDetail');
            Route::post('/data/detail/total', 'JournalController@totalDetail');
            Route::post('/export-pdf', 'JournalController@toPdf');
            Route::post('/export-excel', 'JournalController@toExcel');
        });

        // report
        Route::group(['prefix' => 'report'], function() {
            Route::get('/', 'ReportController@index');

            // transaction
            Route::group(['prefix' => 'transaction'], function() {
                Route::get('/', 'ReportController@indexTransaction');
                Route::post('/data', 'ReportController@dataTransaction');
                Route::post('/export-pdf', 'ReportController@toPdfTransaction');
                Route::post('/export-excel', 'ReportController@toExcelTransaction');
                Route::post('/validate', 'ReportController@validate');
            });

            // ledger
            Route::group(['prefix' => 'ledger'], function() {
                Route::get('/', 'ReportController@indexLedger');
                Route::get('/view', 'ReportController@indexLedgerView');
                Route::post('/export-pdf', 'ReportController@toPdfLedger');
                Route::post('/export-excel', 'ReportController@toExcelLedger');
            });

            // profit-loss
            Route::group(['prefix' => 'profit-loss'], function() {
                Route::get('/', 'ReportController@indexProfitLoss');
                Route::get('/view', 'ReportController@indexProfitLossView');
                Route::post('/export-pdf', 'ReportController@toPdfProfitLoss');
                Route::post('/export-excel', 'ReportController@toExcelProfitLoss');
            });

            // balance-sheet
            Route::group(['prefix' => 'balance-sheet'], function() {
                Route::get('/', 'ReportController@indexBalanceSheet');
                Route::get('/view', 'ReportController@indexBalanceSheetView');
                Route::post('/export-pdf', 'ReportController@toPdfBalanceSheet');
                Route::post('/export-excel', 'ReportController@toExcelBalanceSheet');
            });

            // trial-balance
            Route::group(['prefix' => 'trial-balance'], function() {
                Route::get('/', 'ReportController@indexTrialBalance');
                Route::post('/data', 'ReportController@dataTrialBalance');
                Route::post('/export-pdf', 'ReportController@toPdfTrialBalance');
                Route::post('/export-excel', 'ReportController@toExcelTrialBalance');
            });

            // equity-change
            Route::group(['prefix' => 'equity-change'], function() {
                Route::get('/', 'ReportController@indexEquityChange');
                Route::get('/view', 'ReportController@indexEquityChangeView');
                Route::post('/export-pdf', 'ReportController@toPdfEquityChange');
                Route::post('/export-excel', 'ReportController@toExcelEquityChange');
            });

            // cash-flow
            Route::group(['prefix' => 'cash-flow'], function() {
                Route::get('/', 'ReportController@indexCashFlow');
                Route::get('/view', 'ReportController@indexCashFlowView');
                Route::post('/export-pdf', 'ReportController@toPdfCashFlow');
                Route::post('/export-excel', 'ReportController@toExcelCashFlow');
            });

            // audit
            Route::group(['prefix' => 'audit'], function() {
                Route::get('/', 'ReportController@indexAudit');
                Route::post('/data', 'ReportController@dataAudit');
                Route::post('/export-pdf', 'ReportController@toPdfAudit');
                Route::post('/export-excel', 'ReportController@toExcelAudit');
                Route::get('/view', 'ReportController@indexAuditView');
            });

            // receipt
            Route::group(['prefix' => 'receipt'], function() {
        
                // class
                Route::group(['prefix' => 'class'], function() {
                    Route::get('/', 'ReportReceiptController@indexReceiptClass');
                    Route::get('/view', 'ReportReceiptController@indexReceiptClassView');
                    Route::post('/data', 'ReportReceiptController@dataReceiptClass');
                    Route::post('/export-pdf', 'ReportReceiptController@toPdfReceiptClass');
                    Route::post('/export-excel', 'ReportReceiptController@toExcelReceiptClass');
                });
                // student
                Route::group(['prefix' => 'student'], function() {
                    Route::get('/', 'ReportReceiptController@indexReceiptStudent');
                    Route::get('/mandatory', 'ReportReceiptController@indexReceiptStudentMandatory');
                    Route::post('/export-pdf', 'ReportReceiptController@toPdfReceiptStudent');
                    Route::post('/export-excel', 'ReportReceiptController@toExcelReceiptStudent');

                    // arrear
                    Route::group(['prefix' => 'arrear'], function() {
                        Route::get('/', 'ReportReceiptController@indexReceiptStudentArrear');
                        Route::get('/view', 'ReportReceiptController@indexReceiptStudentArrearView');
                        Route::post('/data', 'ReportReceiptController@dataReceiptStudentArrear');
                        Route::post('/export-pdf', 'ReportReceiptController@toPdfReceiptStudentArrear');
                        Route::post('/export-excel', 'ReportReceiptController@toExcelReceiptStudentArrear');
                    });    

                    Route::group(['prefix' => 'prospect'], function() {
                        Route::get('/', 'ReportReceiptController@indexReceiptProspect');
                        Route::get('/view', 'ReportReceiptController@indexReceiptProspectView');
                        Route::post('/data', 'ReportReceiptController@dataReceiptProspect');
                        Route::post('/export-pdf', 'ReportReceiptController@toPdfReceiptProspect');
                        Route::post('/export-excel', 'ReportReceiptController@toExcelReceiptProspect');
                        
                        // group
                        Route::group(['prefix' => 'group'], function() {
                            Route::get('/', 'ReportReceiptController@indexReceiptStudentProspectGroup');
                            Route::get('/view', 'ReportReceiptController@indexStudentProspectGroupView');
                            Route::post('/data', 'ReportReceiptController@dataStudentProspectGroup');
                            Route::post('/export-pdf', 'ReportReceiptController@toPdfStudentProspectGroup');
                            Route::post('/export-excel', 'ReportReceiptController@toExcelStudentProspectGroup');
                        });        

                        // arrear
                        Route::group(['prefix' => 'arrear'], function() {
                            Route::get('/', 'ReportReceiptController@indexReceiptProspectArrear');
                            Route::get('/view', 'ReportReceiptController@indexReceiptProspectArrearView');
                            Route::post('/data', 'ReportReceiptController@dataReceiptProspectArrear');
                            Route::post('/export-pdf', 'ReportReceiptController@toPdfReceiptProspectArrear');
                            Route::post('/export-excel', 'ReportReceiptController@toExcelReceiptProspectArrear');
                        });        
                    });        
                });
                // recap
                Route::group(['prefix' => 'recap'], function() {
                    Route::get('/', 'ReportReceiptController@indexReceiptRecap');
                    Route::get('/view', 'ReportReceiptController@indexReceiptRecapView');
                    Route::get('/detail', 'ReportReceiptController@indexReceiptRecapDetail');
                    Route::post('/export-pdf', 'ReportReceiptController@toPdfReceiptRecap');
                    Route::post('/export-excel', 'ReportReceiptController@toExcelReceiptRecap');
                    
                    Route::group(['prefix' => 'arrear'], function() {
                        Route::get('/', 'ReportReceiptController@indexReceiptRecapArrear');
                        Route::get('/view', 'ReportReceiptController@indexReceiptRecapArrearView');
                        Route::post('/export-excel', 'ReportReceiptController@toExcelReceiptRecapArrear');
                    });
                });

                // other
                Route::group(['prefix' => 'other'], function() {
                    Route::get('/', 'ReportReceiptController@indexReceiptOther');
                    Route::post('/data', 'ReportReceiptController@dataReceiptOther');
                    Route::post('/export-pdf', 'ReportReceiptController@toPdfReceiptOther');
                    Route::post('/export-excel', 'ReportReceiptController@toExcelReceiptOther');
                });

                // journal
                Route::group(['prefix' => 'journal'], function() {
                    Route::get('/', 'ReportReceiptController@indexReceiptJournal');
                    Route::post('/data', 'ReportReceiptController@dataReceiptJournal');
                    Route::post('/export-pdf', 'ReportReceiptController@toPdfReceiptJournal');
                    Route::post('/export-excel', 'ReportReceiptController@toExcelReceiptJournal');
                });
                
            });

            // expense
            Route::group(['prefix' => 'expense'], function() {

                // transaction
                Route::group(['prefix' => 'transaction'], function() {
                    Route::get('/', 'ReportExpenseController@indexExpenseTrans');
                    Route::post('/data', 'ReportExpenseController@dataExpenseTrans');
                    Route::post('/export-pdf', 'ReportExpenseController@toPdfExpenseTrans');
                    Route::post('/export-excel', 'ReportExpenseController@toExcelExpenseTrans');
                });

                // journal
                Route::group(['prefix' => 'journal'], function() {
                    Route::get('/', 'ReportExpenseController@indexExpenseJournal');
                    Route::post('/data', 'ReportExpenseController@dataExpenseJournal');
                    Route::post('/export-pdf', 'ReportExpenseController@toPdfExpenseJournal');
                    Route::post('/export-excel', 'ReportExpenseController@toExcelExpenseJournal');
                });

            });

            // saving
            Route::group(['prefix' => 'saving'], function() {

                // class
                Route::group(['prefix' => 'class'], function() {
                    Route::get('/', 'ReportSavingController@indexSavingClass');
                    Route::post('/data', 'ReportSavingController@dataSavingClass');
                    Route::post('/export-pdf', 'ReportSavingController@toPdfSavingClass');
                    Route::post('/export-excel', 'ReportSavingController@toExcelSavingClass');
                });

                // student
                Route::group(['prefix' => 'student'], function() {
                    Route::get('/', 'ReportSavingController@indexSavingStudent');
                    Route::get('/view', 'ReportSavingController@viewSavingStudent');
                    Route::post('/export-pdf', 'ReportSavingController@toPdfSavingStudent');
                    Route::post('/export-excel', 'ReportSavingController@toExcelSavingStudent');

                    // recap
                    Route::group(['prefix' => 'recap'], function() {
                        Route::get('/', 'ReportSavingController@indexSavingStudentRecap');
                        Route::get('/view', 'ReportSavingController@viewSavingStudentRecap');
                        Route::get('/detail', 'ReportSavingController@detailSavingStudentRecap');
                        Route::post('/export-pdf', 'ReportSavingController@toPdfSavingStudentRecap');
                        Route::post('/export-excel', 'ReportSavingController@toExcelSavingStudentRecap');
                    });

                });

                // employee
                Route::group(['prefix' => 'employee'], function() {
                    Route::get('/', 'ReportSavingController@indexSavingEmployee');
                    Route::get('/view', 'ReportSavingController@viewSavingEmployee');
                    Route::post('/export-pdf', 'ReportSavingController@toPdfSavingEmployee');
                    Route::post('/export-excel', 'ReportSavingController@toExcelSavingEmployee');

                    // recap
                    Route::group(['prefix' => 'recap'], function() {
                        Route::get('/', 'ReportSavingController@indexSavingEmployeeRecap');
                        Route::get('/view', 'ReportSavingController@viewSavingEmployeeRecap');
                        Route::get('/detail', 'ReportSavingController@detailSavingEmployeeRecap');
                        Route::post('/export-pdf', 'ReportSavingController@toPdfSavingEmployeeRecap');
                        Route::post('/export-excel', 'ReportSavingController@toExcelSavingEmployeeRecap');
                    });

                });

            });

        });
    });
});
