@extends('frontend.layouts.master')

@section('content')
    <div class="banner-homepage">
        <div class="banner-homepage-container">
            <div class="banner-homepage-content">
                <div data-aos="fade-down" data-aos-once="true">
                    <h1>Your Custom Prints, Your Way, On-Demand!</h1>
                </div>

                <div data-aos="fade-down" data-aos-once="true">
                    <ul class="list_slogan_banner">
                        <li>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2C6.49 2 2 6.49 2 12C2 17.51 6.49 22 12 22C17.51 22 22 17.51 22 12C22 6.49 17.51 2 12 2ZM15.59 10.59L12.07 14.11C11.92 14.26 11.73 14.33 11.54 14.33C11.35 14.33 11.15 14.26 11.01 14.11L9.25 12.35C8.95 12.06 8.95 11.58 9.25 11.29C9.54 11 10.02 11 10.31 11.29L11.54 12.52L14.53 9.53C14.82 9.24 15.29 9.24 15.59 9.53C15.88 9.82 15.88 10.3 15.59 10.59Z"
                                    fill="#1ABC9C"></path>
                            </svg>
                            <p class="ml-1">100% free to use</p>
                        </li>
                        <li>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2C6.49 2 2 6.49 2 12C2 17.51 6.49 22 12 22C17.51 22 22 17.51 22 12C22 6.49 17.51 2 12 2ZM15.59 10.59L12.07 14.11C11.92 14.26 11.73 14.33 11.54 14.33C11.35 14.33 11.15 14.26 11.01 14.11L9.25 12.35C8.95 12.06 8.95 11.58 9.25 11.29C9.54 11 10.02 11 10.31 11.29L11.54 12.52L14.53 9.53C14.82 9.24 15.29 9.24 15.59 9.53C15.88 9.82 15.88 10.3 15.59 10.59Z"
                                    fill="#1ABC9C"></path>
                            </svg>
                            <p class="ml-1">300+ High-Quality Products</p>
                        </li>
                        <li>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2C6.49 2 2 6.49 2 12C2 17.51 6.49 22 12 22C17.51 22 22 17.51 22 12C22 6.49 17.51 2 12 2ZM15.59 10.59L12.07 14.11C11.92 14.26 11.73 14.33 11.54 14.33C11.35 14.33 11.15 14.26 11.01 14.11L9.25 12.35C8.95 12.06 8.95 11.58 9.25 11.29C9.54 11 10.02 11 10.31 11.29L11.54 12.52L14.53 9.53C14.82 9.24 15.29 9.24 15.59 9.53C15.88 9.82 15.88 10.3 15.59 10.59Z"
                                    fill="#1ABC9C"></path>
                            </svg>
                            <p class="ml-1">No order minimums</p>
                        </li>
                        <li>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2C6.49 2 2 6.49 2 12C2 17.51 6.49 22 12 22C17.51 22 22 17.51 22 12C22 6.49 17.51 2 12 2ZM15.59 10.59L12.07 14.11C11.92 14.26 11.73 14.33 11.54 14.33C11.35 14.33 11.15 14.26 11.01 14.11L9.25 12.35C8.95 12.06 8.95 11.58 9.25 11.29C9.54 11 10.02 11 10.31 11.29L11.54 12.52L14.53 9.53C14.82 9.24 15.29 9.24 15.59 9.53C15.88 9.82 15.88 10.3 15.59 10.59Z"
                                    fill="#1ABC9C"></path>
                            </svg>
                            <p class="ml-1">Complete automation</p>
                        </li>
                    </ul>
                </div>

                <div data-aos="fade-down" data-aos-once="true" data-aos-delay="300">
                    <a href="/app/login"><button type="button" class="">
                            <span> Start Selling</span>
                        </button></a>
                </div>
            </div>

            <div data-aos="fade-down" data-aos-once="true" data-aos-delay="300" class="banner-homepage-slider">
                <div class="slider-container">
                    <!-- Slider chính -->
                    <div class="swiper main-slider">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s1.png') }}" alt="Image 1" />
                                <div class="px-4 d-flex flex-column gap-2 text-center bg-white">
                                    <span class="text-dark fw-bold text-truncate fs-6">Wooden Picture Frame
                                        Magnet</span>
                                    <p class="text-dark fs-6">Start from $5.49</p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s2.png') }}" alt="Image 2" />
                                <div class="px-4 d-flex flex-column gap-2 text-center bg-white">
                                    <span class="text-dark fw-bold text-truncate fs-6">Wooden Picture Frame
                                        Magnet</span>
                                    <p class="text-dark fs-6">Start from $6.49</p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s3.png') }}" alt="Image 3" />
                                <div class="px-4 d-flex flex-column gap-2 text-center bg-white">
                                    <span class="text-dark fw-bold text-truncate fs-6">Wooden Picture Frame
                                        Magnet</span>
                                    <p class="text-dark fs-6">Start from $7.49</p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s4.png') }}" alt="Image 4" />
                                <div class="px-4 d-flex flex-column gap-2 text-center bg-white">
                                    <span class="text-dark fw-bold text-truncate fs-6">Wooden Picture Frame
                                        Magnet</span>
                                    <p class="text-dark fs-6">Start from $8.49</p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s5.png') }}" alt="Image 4" />
                                <div class="px-4 d-flex flex-column gap-2 text-center bg-white">
                                    <span class="text-dark fw-bold text-truncate fs-6">Wooden Picture Frame
                                        Magnet</span>
                                    <p class="text-dark fs-6">Start from $9.49</p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s6.png') }}" alt="Image 4" />
                                <div class="px-4 d-flex flex-column gap-2 text-center bg-white">
                                    <span class="text-dark fw-bold text-truncate fs-6">Wooden Picture Frame
                                        Magnet</span>
                                    <p class="text-dark fs-6">Start from $10.49</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail (ảnh nhỏ bên phải) -->
                    <div class="swiper thumb-slider">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s1.png') }}" alt="Thumb 1" />
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s2.png') }}" alt="Thumb 1" />
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s3.png') }}" alt="Thumb 1" />
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s4.png') }}" alt="Thumb 1" />
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s5.png') }}" alt="Thumb 1" />
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('frontend/assets/img/s6.png') }}" alt="Thumb 1" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="banner_discount w-100 mx-auto position-relative">
        <div class="banner_discount_container" data-aos="fade-down" data-aos-once="true">
            <a href="">
                <img class="img-fluid" src="{{ asset('frontend/assets/img/motherday.jpg') }}" alt="" />
            </a>
        </div>

        <div class="homepage_benifit position-absolute">
            <div data-aos="fade-down" data-aos-once="true" class="pw_home_benifit">
                <div class="box_benifit_item">
                    <img loading="lazy" alt=" Greater Profits" data-cfsrc="/images/img3.png"
                        src="https://printway.io/images/img3.png" />
                    <div class="box_benifit_item_content">
                        <div class="mb-3">
                            <h3 class="title">Greater Profits</h3>
                        </div>
                        <p class="sub_content">
                            We offer products with the most competitive price to help your
                            business successful.
                        </p>
                    </div>
                </div>
                <div class="box_benifit_item">
                    <img loading="lazy" alt=" Greater Profits" data-cfsrc="/images/img3.png"
                        src="https://printway.io/images/img3.png" />
                    <div class="box_benifit_item_content">
                        <div class="mb-3">
                            <h3 class="title">Greater Profits</h3>
                        </div>
                        <p class="sub_content">
                            We offer products with the most competitive price to help your
                            business successful.
                        </p>
                    </div>
                </div>
                <div class="box_benifit_item">
                    <img loading="lazy" alt=" Greater Profits" data-cfsrc="/images/img3.png"
                        src="https://printway.io/images/img3.png" />
                    <div class="box_benifit_item_content">
                        <div class="mb-3">
                            <h3 class="title">Greater Profits</h3>
                        </div>
                        <p class="sub_content">
                            We offer products with the most competitive price to help your
                            business successful.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="how_it_work_step_wrapper">
        <div data-aos="fade-down" data-aos-once="true" data-aos-delay="400" class="title_content">
            <h2 class="title_highlight">
                4 simple steps for your business to become successful
            </h2>
            <div class="mt-4">
                <p class="title_large">
                    From Your Storefront to Your Customer's Hands
                </p>
            </div>
        </div>

        <div class="how_it_work_step_container">
            <div class="how_it_work_step_inner">
                <div data-aos="fade-right" data-aos-once="true" data-aos-delay="500" class="box_step_wrap">
                    <div class="img_border position-relative d-flex">
                        <img loading="lazy" class="z-10" alt="step1_title" data-cfsrc="/images/step1.png"
                            src="https://printway.io/images/step1.png" />
                        <div class="border_line_box box_1"></div>
                        <div class="position-relative">
                            <div class="circle_step"><span>1</span></div>
                        </div>
                    </div>
                    <div class="box_step_content">
                        <div class="content_step_inner mb-3">
                            <h3 class="title">Connect Store</h3>
                        </div>
                        <p class="sub_title">
                            Connect Your Store To Printway, Add Your Products, And Set Your
                            Own Retail Prices
                        </p>
                    </div>
                </div>
                <div data-aos="fade-right" data-aos-once="true" data-aos-delay="600" class="box_step_wrap">
                    <div class="img_border position-relative d-flex">
                        <img loading="lazy" class="z-10" alt="step2_title" data-cfsrc="/images/step2.png"
                            src="https://printway.io/images/step2.png" />
                        <div class="border_line_box box_2"></div>
                        <div class="position-relative">
                            <div class="circle_step"><span>2</span></div>
                        </div>
                    </div>
                    <div class="box_step_content ml-3 pb-6 max-[480px]:pb-0">
                        <div class="content_step_inner mb-3">
                            <h3 class="title">Customer Places Their Order</h3>
                        </div>
                        <p class="sub_title">
                            A Customer Buys From Your Store, We Charge For Fulfillment, And
                            You Keep The Profit
                        </p>
                    </div>
                </div>
                <div data-aos="fade-right" data-aos-once="true" data-aos-delay="700" class="box_step_wrap">
                    <div class="img_border position-relative d-flex">
                        <img loading="lazy" class="z-10" alt="step3_title" data-cfsrc="/images/step3.png"
                            src="https://printway.io/images/step3.png" />
                        <div class="border_line_box box_3"></div>
                        <div class="position-relative">
                            <div class="circle_step"><span>3</span></div>
                        </div>
                    </div>
                    <div class="box_step_content ml-3 pb-6 max-[480px]:pb-0">
                        <div class="content_step_inner mb-3">
                            <h3 class="title">Printway Fulfills The Order</h3>
                        </div>
                        <p class="sub_title">
                            We Take Care Of Your Order From A To Z, And Control The Whole
                            Fulfillment Process
                        </p>
                    </div>
                </div>
                <div data-aos="fade-right" data-aos-once="true" data-aos-delay="800" class="box_step_wrap">
                    <div class="img_border position-relative d-flex">
                        <img loading="lazy" class="z-10" alt="step4_title" data-cfsrc="/images/step4.png"
                            src="https://printway.io/images/step4.png" />
                        <div class="border_line_box box_4"></div>
                        <div class="position-relative">
                            <div class="circle_step"><span>4</span></div>
                        </div>
                    </div>
                    <div class="box_step_content ml-3">
                        <div class="content_step_inner mb-3">
                            <h3 class="title">Order Ships To Your Customer</h3>
                        </div>
                        <p class="sub_title">
                            Your Customer Receives Their Order With Your Brand Attached To
                            It
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div data-aos="fade-down" data-aos-once="true" data-aos-delay="500" class="pw_category_product_wrapper">
        <div class="title_content">
            <h2 class="title_highlight">Highlight Categories</h2>
            <div class="mt-4">
                <p class="title_large">
                    Customize products &amp; start selling in minutes
                </p>
            </div>
        </div>
        <div data-aos="fade-down" data-aos-once="true" data-aos-delay="600" class="category_home">
            <div class="categories">
                <div class="list_categories">
                    <div class="row text-center">
                        <div class="col-md-3 col-6">
                            <div class="category-card shadow">
                                <img src="https://cdn.printway.io/lzi/666c0e896f96e33ccef7c815_800x800.jpeg"
                                    alt="Women's Clothing" />
                                <h5 class="mt-3 title_name">Women's Clothing</h5>
                                <p class="quantity_text">43 Products</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="category-card shadow">
                                <img src="https://cdn.printway.io/lzi/666c00e33dea86065a84db26_800x800.jpeg"
                                    alt="Men's Clothing" />
                                <h5 class="mt-3 title_name">Men's Clothing</h5>
                                <p class="quantity_text">45 Products</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="category-card shadow">
                                <img src="https://cdn.printway.io/lzi/666bf7e8e9f8ecbc0656be49_800x800.jpeg"
                                    alt="Accessories" />
                                <h5 class="mt-3 title_name">Accessories</h5>
                                <p class="quantity_text">67 Products</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="category-card shadow">
                                <img src="https://cdn.printway.io/lzi/666bc61b7ecd3a7dbd7c4624_800x800.jpeg"
                                    alt="Home and Living" />
                                <h5 class="mt-3 title_name">Home and Living</h5>
                                <p class="quantity_text">212 Products</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-center w-100 mt-2">
                <a href="#">
                    <button type="button" class="ant-btn-primary">
                        <span> View all products</span>
                    </button></a>
            </div>
        </div>
    </div>

    <div class="pw_different_part_wrapper position-relative">
        <div class="pw_help_you_wrapper">
            <div data-aos="zoom-out" data-aos-once="true" data-aos-delay="350"
                class="w-100 d-flex align-items-center justify-content-center position-absolute z-3" style="top: -55px">
                <img loading="lazy" src="https://printway.io/images/logo_line.png" alt="printway.io" />
            </div>

            <div data-aos="fade-up" data-aos-once="true" data-aos-delay="400" class="pw_help_you_wrapper_inner">
                <div class="title_content">
                    <h2 class="title_highlight">What will Printway help you</h2>
                    <div class="mt-4">
                        <p class="title_large">
                            We're different, empowering your business to thrive
                        </p>
                    </div>
                </div>

                <div class="pw_help_you_content">
                    <div class="list_different_boxes row g-4">
                        <div class="col-12 col-sm-6 col-lg-4" data-aos="flip-up" data-aos-once="true"
                            data-aos-delay="400">
                            <div class="box_different_item d-flex flex-column h-100">
                                <div class="image_different">
                                    <img loading="lazy" src="https://printway.io/images/dif1.png" alt="DIFF_TITLE_1" />
                                </div>
                                <div class="content_cate_box content_different">
                                    <h3 class="title_name">Free to Use</h3>
                                    <p class="quantity_text">
                                        Only Pay For Fulfillment &amp; Shipping when orders Come
                                        In
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4" data-aos="flip-up" data-aos-once="true"
                            data-aos-delay="450">
                            <div class="box_different_item d-flex flex-column h-100">
                                <div class="image_different">
                                    <img loading="lazy" src="https://printway.io/images/dif2.png" alt="DIFF_TITLE_2" />
                                </div>
                                <div class="content_cate_box content_different">
                                    <h3 class="title_name">No Order Minimus</h3>
                                    <p class="quantity_text">
                                        Enjoy The Freedom to experiment with your store concept
                                        and products with no order minimums
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4" data-aos="flip-up" data-aos-once="true"
                            data-aos-delay="500">
                            <div class="box_different_item d-flex flex-column h-100">
                                <div class="image_different">
                                    <img loading="lazy" src="https://printway.io/images/dif3.png" alt="DIFF_TITLE_3" />
                                </div>
                                <div class="content_cate_box content_different">
                                    <h3 class="title_name">Control the Entire Progress</h3>
                                    <p class="quantity_text">
                                        All of your orders are under your control from production
                                        to shipping
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4" data-aos="flip-up" data-aos-once="true"
                            data-aos-delay="550">
                            <div class="box_different_item d-flex flex-column h-100">
                                <div class="image_different">
                                    <img loading="lazy" src="https://printway.io/images/dif4.png" alt="DIFF_TITLE_4" />
                                </div>
                                <div class="content_cate_box content_different">
                                    <h3 class="title_name">Complete automation</h3>
                                    <p class="quantity_text">
                                        Our integrations allow automatic order import from your
                                        store
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4" data-aos="flip-up" data-aos-once="true"
                            data-aos-delay="600">
                            <div class="box_different_item d-flex flex-column h-100">
                                <div class="image_different">
                                    <img loading="lazy" src="https://printway.io/images/dif5.png" alt="DIFF_TITLE_5" />
                                </div>
                                <div class="content_cate_box content_different">
                                    <h3 class="title_name">Custom branding</h3>
                                    <p class="quantity_text">
                                        We fulfill &amp; ship everything under your brand with
                                        custom label, pack-ins, and more
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4" data-aos="flip-up" data-aos-once="true"
                            data-aos-delay="650">
                            <div class="box_different_item d-flex flex-column h-100">
                                <div class="image_different">
                                    <img loading="lazy" src="https://printway.io/images/dif6.png" alt="DIFF_TITLE_6" />
                                </div>
                                <div class="content_cate_box content_different">
                                    <h3 class="title_name">24/7 Support</h3>
                                    <p class="quantity_text">
                                        Our consulting team is ready to support you 24/7. We
                                        always care about everything you need.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div data-aos="fade-up" data-aos-once="true" data-aos-delay="600"
                        class="d-flex justify-content-center align-items-center mt-5">
                        <a href="#"><button type="button"
                                class="ant-btn css-2i2tap ant-btn-primary btn_primary_ant">
                                <span>Start Your Bussiness</span>
                            </button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
