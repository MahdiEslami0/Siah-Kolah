 

<?php $__env->startPush('libraries_top'); ?>
    <link rel="stylesheet" href="/assets/admin/vendor/owl.carousel/owl.carousel.min.css">
    <link rel="stylesheet" href="/assets/admin/vendor/owl.carousel/owl.theme.min.css">

<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


    <section class="section">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="hero text-white hero-bg-image hero-bg" data-background="<?php echo e(!empty(getPageBackgroundSettings('admin_dashboard')) ? getPageBackgroundSettings('admin_dashboard') : ''); ?>">
                    <div class="hero-inner">
                        <h2><?php echo e(trans('admin/main.welcome')); ?>, <?php echo e($authUser->full_name); ?>!</h2>

                        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_general_dashboard_quick_access_links')): ?>
                                <div>
                                    <p class="lead"><?php echo e(trans('admin/main.welcome_card_text')); ?></p>

                                    <div class="mt-2 mb-2 d-flex flex-column flex-md-row">
                                        <a href="<?php echo e(getAdminPanelUrl()); ?>/comments/webinars" class="mt-2 mt-md-0 btn btn-outline-white btn-lg btn-icon icon-left ml-0 ml-md-2"><i class="far fa-comment"></i><?php echo e(trans('admin/main.comments')); ?> </a>
                                        
                                        <a href="<?php echo e(getAdminPanelUrl()); ?>/reports/webinars" class="mt-2 mt-md-0 btn btn-outline-white btn-lg btn-icon icon-left ml-0 ml-md-2"><i class="fas fa-info"></i><?php echo e(trans('admin/main.reports')); ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_clear_cache')): ?>
                                <div class="w-xs-to-lg-100">
                                    <p class="lead d-none d-lg-block">&nbsp;</p>

                                    <?php echo $__env->make('admin.includes.delete_button',[
                                             'url' => getAdminPanelUrl().'/clear-cache',
                                             'btnClass' => 'btn btn-outline-white btn-lg btn-icon icon-left mt-2 w-100',
                                             'btnText' => trans('admin/main.clear_all_cache'),
                                             'hideDefaultClass' => true
                                          ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            


            <div class="col-lg-6 col-md-6 col-sm-12">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_general_dashboard_income_statistics')): ?>
                    <?php if(!empty($getIncomeStatistics)): ?>
                        <div class="card card-statistic-2">
                            <div class="card-stats">
                                <div class="card-stats-title"><?php echo e(trans('admin/main.income_statistics')); ?></div>

                                <div class="card-stats-items">
                                    <div class="card-stats-item">
                                        <div class="card-stats-item-count"><?php echo e(handlePrice($getIncomeStatistics['todaySales'])); ?></div>
                                        <div class="card-stats-item-label"><?php echo e(trans('admin/main.today')); ?></div>
                                    </div>

                                    <div class="card-stats-item">
                                        <div class="card-stats-item-count"><?php echo e(handlePrice($getIncomeStatistics['monthSales'])); ?></div>
                                        <div class="card-stats-item-label"><?php echo e(trans('admin/main.this_month')); ?></div>
                                    </div>

                                    <div class="card-stats-item">
                                        <div class="card-stats-item-count"><?php echo e(handlePrice($getIncomeStatistics['yearSales'])); ?></div>
                                        <div class="card-stats-item-label"><?php echo e(trans('admin/main.this_year')); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4><?php echo e(trans('admin/main.total_incomes')); ?></h4>
                                </div>
                                <div class="card-body">
                                    <?php echo e(handlePrice($getIncomeStatistics['totalSales'])); ?>

                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_general_dashboard_total_sales_statistics')): ?>
                    <?php if(!empty($getTotalSalesStatistics)): ?>
                        <div class="card card-statistic-2">
                            <div class="card-stats">
                                <div class="card-stats-title"><?php echo e(trans('admin/main.salescount')); ?></div>

                                <div class="card-stats-items">
                                    <div class="card-stats-item">
                                        <div class="card-stats-item-count"><?php echo e($getTotalSalesStatistics['todaySales']); ?></div>
                                        <div class="card-stats-item-label"><?php echo e(trans('admin/main.today')); ?></div>
                                    </div>
                                    <div class="card-stats-item">
                                        <div class="card-stats-item-count"><?php echo e($getTotalSalesStatistics['monthSales']); ?></div>
                                        <div class="card-stats-item-label"><?php echo e(trans('admin/main.this_month')); ?></div>
                                    </div>
                                    <div class="card-stats-item">
                                        <div class="card-stats-item-count"><?php echo e($getTotalSalesStatistics['yearSales']); ?></div>
                                        <div class="card-stats-item-label"><?php echo e(trans('admin/main.this_year')); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-shopping-cart"></i>
                            </div>

                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4><?php echo e(trans('admin/main.total_sales')); ?></h4>
                                </div>
                                <div class="card-body">
                                    <?php echo e($getTotalSalesStatistics['totalSales']); ?>

                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_general_dashboard_new_sales')): ?>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <a href="<?php echo e(getAdminPanelUrl()); ?>/financial/sales" class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?php echo e(trans('admin/main.new_sale')); ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo e($getNewSalesCount); ?>

                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_general_dashboard_new_comments')): ?>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <a href="<?php echo e(getAdminPanelUrl()); ?>/comments/webinars" class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-comment"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?php echo e(trans('admin/main.new_comment')); ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo e($getNewCommentsCount); ?>

                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_general_dashboard_new_reviews')): ?>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <a class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-eye"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?php echo e(trans('admin/main.pending_review_classes')); ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo e($getPendingReviewCount); ?>

                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

        </div>


        <div class="row">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_general_dashboard_sales_statistics_chart')): ?>
                <div class="col-lg-8 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><?php echo e(trans('admin/main.sales_statistics')); ?></h4>
                            <div class="card-header-action">
                                <div class="btn-group">
                                    <button type="button" class="js-sale-chart-month btn"><?php echo e(trans('admin/main.month')); ?></button>
                                    <button type="button" class="js-sale-chart-year btn btn-primary"><?php echo e(trans('admin/main.year')); ?></button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="position-relative">
                                        <canvas id="saleStatisticsChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <?php if(!empty($getMonthAndYearSalesChartStatistics)): ?>
                                        <div class="statistic-details mt-4 position-relative">
                                            <div class="statistic-details-item">
                                                <span class="text-muted">
                                                    <?php if($getMonthAndYearSalesChartStatistics['todaySales']['grow_percent']['status'] == 'up'): ?>
                                                        <span class="text-primary"><i class="fas fa-caret-up"></i></span>
                                                    <?php else: ?>
                                                        <span class="text-danger"><i class="fas fa-caret-down"></i></span>
                                                    <?php endif; ?>

                                                    <?php echo e($getMonthAndYearSalesChartStatistics['todaySales']['grow_percent']['percent']); ?>

                                                </span>

                                                <div class="detail-value"><?php echo e(handlePrice($getMonthAndYearSalesChartStatistics['todaySales']['amount'])); ?></div>
                                                <div class="detail-name"><?php echo e(trans('admin/main.today_sales')); ?></div>
                                            </div>
                                            <div class="statistic-details-item">
                                                <span class="text-muted">
                                                    <?php if($getMonthAndYearSalesChartStatistics['weekSales']['grow_percent']['status'] == 'up'): ?>
                                                        <span class="text-primary"><i class="fas fa-caret-up"></i></span>
                                                    <?php else: ?>
                                                        <span class="text-danger"><i class="fas fa-caret-down"></i></span>
                                                    <?php endif; ?>

                                                    <?php echo e($getMonthAndYearSalesChartStatistics['weekSales']['grow_percent']['percent']); ?>

                                                </span>

                                                <div class="detail-value"><?php echo e(handlePrice($getMonthAndYearSalesChartStatistics['weekSales']['amount'])); ?></div>
                                                <div class="detail-name"><?php echo e(trans('admin/main.week_sales')); ?></div>
                                            </div>
                                            <div class="statistic-details-item">
                                                <span class="text-muted">
                                                    <?php if($getMonthAndYearSalesChartStatistics['monthSales']['grow_percent']['status'] == 'up'): ?>
                                                        <span class="text-primary"><i class="fas fa-caret-up"></i></span>
                                                    <?php else: ?>
                                                        <span class="text-danger"><i class="fas fa-caret-down"></i></span>
                                                    <?php endif; ?>

                                                    <?php echo e($getMonthAndYearSalesChartStatistics['monthSales']['grow_percent']['percent']); ?>

                                                </span>

                                                <div class="detail-value"><?php echo e(handlePrice($getMonthAndYearSalesChartStatistics['monthSales']['amount'])); ?></div>
                                                <div class="detail-name"><?php echo e(trans('admin/main.month_sales')); ?></div>
                                            </div>
                                            <div class="statistic-details-item">
                                                <span class="text-muted">
                                                    <?php if($getMonthAndYearSalesChartStatistics['yearSales']['grow_percent']['status'] == 'up'): ?>
                                                        <span class="text-primary"><i class="fas fa-caret-up"></i></span>
                                                    <?php else: ?>
                                                        <span class="text-danger"><i class="fas fa-caret-down"></i></span>
                                                    <?php endif; ?>

                                                    <?php echo e($getMonthAndYearSalesChartStatistics['yearSales']['grow_percent']['percent']); ?>

                                                </span>

                                                <div class="detail-value"><?php echo e(handlePrice($getMonthAndYearSalesChartStatistics['yearSales']['amount'])); ?></div>
                                                <div class="detail-name"><?php echo e(trans('admin/main.year_sales')); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_general_dashboard_recent_comments')): ?>
                <div class="col-lg-4 col-md-12 col-12 col-sm-12 <?php if(count($recentComments) < 6): ?> pb-30 <?php endif; ?>">
                    <div class="card <?php if(count($recentComments) < 6): ?> h-100 <?php endif; ?>">
                        <div class="card-header">
                            <h4><?php echo e(trans('admin/main.recent_comments')); ?></h4>
                        </div>

                        <div class="card-body d-flex flex-column justify-content-between">
                            <ul class="list-unstyled list-unstyled-border">
                                <?php $__currentLoopData = $recentComments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recentComment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="media">
                                        <img class="mr-3 rounded-circle" width="50" height="50" src="<?php echo e($recentComment->user->getAvatar()); ?>" alt="avatar">
                                        <div class="media-body">
                                            <div class="float-right text-primary font-12"><?php echo e(dateTimeFormat($recentComment->created_at, 'j M Y | H:i')); ?></div>
                                            <div class="media-title"><?php echo e($recentComment->user->full_name); ?></div>
                                            <span class="text-small text-muted"><?php echo e(truncate($recentComment->comment, 150)); ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>

                            <div class="text-center pt-1 pb-1">
                                <a href="<?php echo e(getAdminPanelUrl()); ?>/comments/webinars" class="btn btn-primary btn-lg btn-round ">
                                    <?php echo e(trans('admin/main.view_all')); ?>

                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>


        

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_general_dashboard_users_statistics_chart')): ?>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><?php echo e(trans('admin/main.new_registration_statistics')); ?></h4>
                            <div class="card-header-action">
                                <div class="btn-group">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="position-relative">
                                        <canvas id="usersStatisticsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts_bottom'); ?>
    <script src="/assets/default/vendors/chartjs/chart.min.js"></script>
    <script src="/assets/admin/vendor/owl.carousel/owl.carousel.min.js"></script>

    <script src="/assets/admin/js/dashboard.min.js"></script>

    <script>
        (function ($) {
            "use strict";

            <?php if(!empty($getMonthAndYearSalesChart)): ?>
            makeStatisticsChart('saleStatisticsChart', saleStatisticsChart, 'Sale', <?php echo json_encode($getMonthAndYearSalesChart['labels'], 15, 512) ?>,<?php echo json_encode($getMonthAndYearSalesChart['data'], 15, 512) ?>);
            <?php endif; ?>

            <?php if(!empty($usersStatisticsChart)): ?>
            makeStatisticsChart('usersStatisticsChart', usersStatisticsChart, 'Users', <?php echo json_encode($usersStatisticsChart['labels'], 15, 512) ?>,<?php echo json_encode($usersStatisticsChart['data'], 15, 512) ?>);
            <?php endif; ?>

        })(jQuery)
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\projects\Rocket Lms\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>