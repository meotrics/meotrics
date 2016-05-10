@extends('layout.master')
@section('title', 'Meotrics')
@section('style')

@endsection
@section('script')
@endsection

@section('content')
	<!--THIS IS HOME {{cookie('laravel_session')}}-->
    <div class="row">
        <div class="card col-md-12">
            <div class="row">
                <div class="content col-md-12">
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Number of visitors</p>
                                <h3>430</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Today <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Number of new visitors</p>
                                <h3>23</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Today <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>User Growth rate</p>
                                <h3>5%</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Today <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Retention rate</p>
                                <h3>2.5%</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">This week <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Purchase Conversion rate</p>
                                <h3>0.1%</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer color-red">Low</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Total revenue</p>
                                <h3>2383<i class="fa fa-dollar font-size-20"></i></h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">This week <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Revenue per customer</p>
                                <h3>15.3<i class="fa fa-dollar font-size-20"></i></h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">This week <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Highest revenue campaign</p>
                                <h3>Facebook</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer"></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Most effective Referrer</p>
                                <h3>Social</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('additional')
  @include('partials/install_guide')
@endsection
