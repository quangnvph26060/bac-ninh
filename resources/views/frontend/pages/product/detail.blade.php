@extends('frontend.layouts.master')

@section('content')
    <div class="pw_product_wrapper">
        <div class="breadcrumb_wrapper">
            <div class="d-flex align-items-center mt-2 mb-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Library
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="detail_product mb-5 mt-3">
            <div class="d-flex justify-content-between gap-4">
                <div class="image_product">
                    <div class="swiper swiper-thumbnail">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="https://cdn.printway.io/lzi/668baeaea71d0121db1373be_800x800.png"
                                    alt="Thumb 1" />
                            </div>
                            <div class="swiper-slide">
                                <img src="	https://cdn.printway.io/lzi/668baeaf938d65334eefda4c_800x800.png"
                                    alt="Thumb 2" />
                            </div>
                            <div class="swiper-slide">
                                <img src="https://cdn.printway.io/lzi/668baeaf2f0e499711f838da_800x800.png"
                                    alt="Thumb 3" />
                            </div>
                            <div class="swiper-slide">
                                <img src="https://cdn.printway.io/lzi/668baeaf8eb036f21edb94b1_800x800.png"
                                    alt="Thumb 4" />
                            </div>
                        </div>
                    </div>

                    <div class="swiper swiper-main">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="https://cdn.printway.io/lzi/668baeaea71d0121db1373be_800x800.png"
                                    alt="Main 1" />
                            </div>
                            <div class="swiper-slide">
                                <img src="https://cdn.printway.io/lzi/668baeaf938d65334eefda4c_800x800.png"
                                    alt="Main 2" />
                            </div>
                            <div class="swiper-slide">
                                <img src="https://cdn.printway.io/lzi/668baeaf2f0e499711f838da_800x800.png"
                                    alt="Main 3" />
                            </div>
                            <div class="swiper-slide">
                                <img src="https://cdn.printway.io/lzi/668baeaf8eb036f21edb94b1_800x800.png"
                                    alt="Main 4" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info_product">
                    <div class="w-100">
                        <div class="mb-4">
                            <h1 class="fs-4 fw-bold text-dark product_name">
                                Custom 3 Layered Sun Visor Clip
                            </h1>
                        </div>

                        <div class="mb-4">
                            <p class="fw-bold text-dark text-base">Size</p>
                            <div class="d-flex gap-3 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="size" id="size1" />
                                    <label class="form-check-label" for="size1">6X6 INCHES</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="size" id="size2" />
                                    <label class="form-check-label" for="size2">5X5 INCHES</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="size" id="size3" />
                                    <label class="form-check-label" for="size3">4X4 INCHES</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="fw-bold text-dark text-base">Style</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="style" id="style1" />
                                    <label class="form-check-label" for="style1">1 PHOTO</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="style" id="style2" />
                                    <label class="form-check-label" for="style2">3 PHOTOS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="style" id="style3" />
                                    <label class="form-check-label" for="style3">NO PHOTO</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="style" id="style4" />
                                    <label class="form-check-label" for="style4">6 PHOTOS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="style" id="style5" />
                                    <label class="form-check-label" for="style5">9 PHOTOS</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="fw-bold text-dark text-base">Material</p>
                            <div class="d-flex gap-3 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material" id="material1" />
                                    <label class="form-check-label" for="material1">WOOD AND ACRYLIC</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material" id="material2" />
                                    <label class="form-check-label" for="material2">WOOD AND WOOD</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="text-secondary mb-0 text-base">Price*</p>
                            <p class="fs-2 fw-bold text-dark mb-0">10.04 USD</p>
                            <p class="text-success fw-medium">$4.49 with Diamond Tier</p>
                        </div>

                        <div class="d-flex flex-column flex-md-row gap-3">
                            <a href="/app/login" class="w-100">
                                <button type="button" class="ant-btn-primary">
                                    Start new order
                                </button>
                            </a>
                            <!-- <a
                                href="/app/product/public-to-store?producttype=664dd035c8fadc5bfb4639c8"
                                class="w-100"
                            >
                                <button type="button" class="btn btn-outline-secondary w-100">
                                    Template mockup
                                </button>
                            </a> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box_content">
            <h2>
                <!-- -->About<!-- -->
                <!-- -->Custom 3 Layered Sun Visor Clip
            </h2>
            <div class="box_content">
                <div class="textDescription">
                    <p>
                        Add a personal touch to your car with the
                        <a href="https://printway.io/product/custom-3-layered-sun-visor-clip-csvc3l">Custom 3-Layered Sun
                            Visor Clip</a>
                        from Printway. This
                        <a href="https://printway.io/product-category/accessories/car-accessories">print-on-demand
                            accessory</a>
                        offers a unique and stylish way to keep essentials within reach,
                        featuring a customizable, three-layered design that stands out.
                        Perfect for personalization or branding, it’s a practical and
                        creative addition to any vehicle. With no order minimums and a
                        minimum order quantity (MOQ) of just 1, bring your custom designs
                        to life with Printway’s print-on-demand service today!
                    </p>
                </div>
            </div>
        </div>

        <div class="_tab_custom">
            <ul class="nav nav-tabs border-bottom w-100" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                        data-bs-target="#description" type="button" role="tab">
                        Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing"
                        type="button" role="tab">
                        Pricing
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping"
                        type="button" role="tab">
                        Shipping
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="guidelines-tab" data-bs-toggle="tab" data-bs-target="#guidelines"
                        type="button" role="tab">
                        File Guidelines
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3" id="myTabContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="mt-5 _content_ck_editor">
                        <p>
                            <span style="color: rgba(0, 0, 0, 0.88)"><strong>KEY FEATURES</strong></span>
                        </p>
                        <ul>
                            <li>
                                <span style="color: #000000"><strong>Best material</strong>:&nbsp;</span><span
                                    style="color: #0d0d0d">Crafted with high</span><span style="color: #000000">-quality
                                    Wooden and Acrylic for the polaroid and the
                                    stainless steel for the visor clip.</span>
                            </li>
                            <li>
                                <span style="color: #000000"><strong>Perfect Design:&nbsp; </strong>What sets this visor
                                    clip apart is its customizable photo polaroid design. You
                                    can add your favorite photo – whether it's a cherished
                                    memory, a beloved pet, or a special moment – to the clip,
                                    creating a personalized accessory that reflects your unique
                                    style and personality. Everytime you drive, you'll be
                                    reminded of the special memories captured in the photo
                                    polaroid. Whether you're driving to work, running errands,
                                    or embarking on a road trip, the Custom Sun Visor Clip
                                    ensures that your eyewear is always close at hand. It's also
                                    a thoughtful and heartfelt gift for friends and family,
                                    allowing them to carry their favorite memories with them
                                    wherever they go. Add a personal touch to your driving
                                    experience with the Custom Photo Polaroid Sun Visor Clip,
                                    and turn every journey into a nostalgic adventure filled
                                    with cherished memories.</span>
                            </li>
                            <li>
                                <span style="color: #000000"><strong>Gift of Love:</strong>&nbsp;Perfect gift for
                                    Dad&nbsp;</span>
                            </li>
                            <li>
                                <span style="color: #000000"><strong>Customization</strong>: Design will be printed one
                                    side of all the layers&nbsp; by our UV printed
                                    technology</span>
                            </li>
                        </ul>
                        <p>&nbsp;</p>
                        <p><strong>SPECIFICATION</strong></p>
                        <ul>
                            <li>
                                <strong>Processing Time:</strong>&nbsp;3 – 4 working days on
                                average after payment and all designs updated correctly
                            </li>
                            <li>
                                <strong>Shipping Time:</strong> 5&nbsp;– 15 working days on
                                average (US)
                            </li>
                            <li>
                                <strong>Packaging:</strong>1 x&nbsp;<strong> </strong>Custom
                                Sun Visor Clip
                            </li>
                        </ul>
                        <p>&nbsp;</p>
                        <p><strong>PRODUCTION</strong></p>
                        <ul>
                            <li>Manufacturer:&nbsp;Made by Printway</li>
                            <li>The tracking Country Origin is the US</li>
                        </ul>
                        <p>&nbsp;</p>
                        <p><strong>NOTE:</strong></p>
                        <ul>
                            <li>
                                Actual color may be slightly different from the image due to
                                different monitor and light effects.
                            </li>
                            <li>
                                Please allow 0.5-2 cm differences due to manual measurement.
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-pane fade" id="pricing" role="tabpanel">
                    <p>Content for Pricing tab...</p>
                </div>
                <div class="tab-pane fade" id="shipping" role="tabpanel">
                    <p>Content for Shipping tab...</p>
                </div>
                <div class="tab-pane fade" id="guidelines" role="tabpanel">
                    <p>Content for File Guidelines tab...</p>
                </div>
            </div>
        </div>

        <div class="suggest_product_wrapper my-5">
            <div class="suggest_product_wrapper__inner p-0">
                <div class="d-flex justify-content-center">
                    <h2 class="title_suggest_prd">You might also like</h2>
                </div>
                <div class="list_new_arrival">
                    <div class="mx-auto mt-4 mt-md-8 p-xl-0" style="max-width: 1182px">
                        <div class="swiper-container">
                            <div class="swiper my_suggest_product">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <a href="#">
                                            <div class="mockup_prd_wrapper">
                                                <div class="position-relative d-flex justify-content-center mockup_img">
                                                    <img loading="lazy" class="img_prd object-cover"
                                                        src="https://cdn.printway.io/lzi/64f07f1b268e91c78bc75969_800x800.png"
                                                        alt="Transparent Acrylic Car Ornament"
                                                        style="border-radius: 4px 4px 0px 0px" />
                                                </div>
                                                <div class="content_prd_card" style="border-radius: 0px 0px 4px 4px">
                                                    <h3 class="name_prd wrap_two_line_mb">
                                                        Transparent Acrylic Car Ornament
                                                    </h3>
                                                    <p class="text_color text-sm">Start from $2.65</p>
                                                    <div
                                                        class="text-nature-70 text-sm d-flex flex-wrap gap-x-2 align-items-start h-10">
                                                        <p class="title text-[12px] sm:text-sm">
                                                            1 Material
                                                        </p>
                                                        <p class="title text-[12px] sm:text-sm">1 Size</p>
                                                        <p class="title text-[12px] sm:text-sm">
                                                            2 Titles
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="swiper-slide">
                                        <a href="#">
                                            <div class="mockup_prd_wrapper">
                                                <div class="position-relative d-flex justify-content-center mockup_img">
                                                    <img loading="lazy" class="img_prd object-cover"
                                                        src="https://cdn.printway.io/lzi/64f07f1b268e91c78bc75969_800x800.png"
                                                        alt="Transparent Acrylic Car Ornament"
                                                        style="border-radius: 4px 4px 0px 0px" />
                                                </div>
                                                <div class="content_prd_card" style="border-radius: 0px 0px 4px 4px">
                                                    <h3 class="name_prd wrap_two_line_mb">
                                                        Transparent Acrylic Car Ornament
                                                    </h3>
                                                    <p class="text_color text-sm">Start from $2.65</p>
                                                    <div
                                                        class="text-nature-70 text-sm d-flex flex-wrap gap-x-2 align-items-start h-10">
                                                        <p class="title text-[12px] sm:text-sm">
                                                            1 Material
                                                        </p>
                                                        <p class="title text-[12px] sm:text-sm">1 Size</p>
                                                        <p class="title text-[12px] sm:text-sm">
                                                            2 Titles
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="swiper-slide">
                                        <a href="#">
                                            <div class="mockup_prd_wrapper">
                                                <div class="position-relative d-flex justify-content-center mockup_img">
                                                    <img loading="lazy" class="img_prd object-cover"
                                                        src="https://cdn.printway.io/lzi/64f07f1b268e91c78bc75969_800x800.png"
                                                        alt="Transparent Acrylic Car Ornament"
                                                        style="border-radius: 4px 4px 0px 0px" />
                                                </div>
                                                <div class="content_prd_card" style="border-radius: 0px 0px 4px 4px">
                                                    <h3 class="name_prd wrap_two_line_mb">
                                                        Transparent Acrylic Car Ornament
                                                    </h3>
                                                    <p class="text_color text-sm">Start from $2.65</p>
                                                    <div
                                                        class="text-nature-70 text-sm d-flex flex-wrap gap-x-2 align-items-start h-10">
                                                        <p class="title text-[12px] sm:text-sm">
                                                            1 Material
                                                        </p>
                                                        <p class="title text-[12px] sm:text-sm">1 Size</p>
                                                        <p class="title text-[12px] sm:text-sm">
                                                            2 Titles
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="swiper-slide">
                                        <a href="#">
                                            <div class="mockup_prd_wrapper">
                                                <div class="position-relative d-flex justify-content-center mockup_img">
                                                    <img loading="lazy" class="img_prd object-cover"
                                                        src="https://cdn.printway.io/lzi/64f07f1b268e91c78bc75969_800x800.png"
                                                        alt="Transparent Acrylic Car Ornament"
                                                        style="border-radius: 4px 4px 0px 0px" />
                                                </div>
                                                <div class="content_prd_card" style="border-radius: 0px 0px 4px 4px">
                                                    <h3 class="name_prd wrap_two_line_mb">
                                                        Transparent Acrylic Car Ornament
                                                    </h3>
                                                    <p class="text_color text-sm">Start from $2.65</p>
                                                    <div
                                                        class="text-nature-70 text-sm d-flex flex-wrap gap-x-2 align-items-start h-10">
                                                        <p class="title text-[12px] sm:text-sm">
                                                            1 Material
                                                        </p>
                                                        <p class="title text-[12px] sm:text-sm">1 Size</p>
                                                        <p class="title text-[12px] sm:text-sm">
                                                            2 Titles
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="swiper-slide">
                                        <a href="#">
                                            <div class="mockup_prd_wrapper">
                                                <div class="position-relative d-flex justify-content-center mockup_img">
                                                    <img loading="lazy" class="img_prd object-cover"
                                                        src="https://cdn.printway.io/lzi/64f07f1b268e91c78bc75969_800x800.png"
                                                        alt="Transparent Acrylic Car Ornament"
                                                        style="border-radius: 4px 4px 0px 0px" />
                                                </div>
                                                <div class="content_prd_card" style="border-radius: 0px 0px 4px 4px">
                                                    <h3 class="name_prd wrap_two_line_mb">
                                                        Transparent Acrylic Car Ornament
                                                    </h3>
                                                    <p class="text_color text-sm">Start from $2.65</p>
                                                    <div
                                                        class="text-nature-70 text-sm d-flex flex-wrap gap-x-2 align-items-start h-10">
                                                        <p class="title text-[12px] sm:text-sm">
                                                            1 Material
                                                        </p>
                                                        <p class="title text-[12px] sm:text-sm">1 Size</p>
                                                        <p class="title text-[12px] sm:text-sm">
                                                            2 Titles
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="swiper-slide">
                                        <a href="#">
                                            <div class="mockup_prd_wrapper">
                                                <div class="position-relative d-flex justify-content-center mockup_img">
                                                    <img loading="lazy" class="img_prd object-cover"
                                                        src="https://cdn.printway.io/lzi/64f07f1b268e91c78bc75969_800x800.png"
                                                        alt="Transparent Acrylic Car Ornament"
                                                        style="border-radius: 4px 4px 0px 0px" />
                                                </div>
                                                <div class="content_prd_card" style="border-radius: 0px 0px 4px 4px">
                                                    <h3 class="name_prd wrap_two_line_mb">
                                                        Transparent Acrylic Car Ornament
                                                    </h3>
                                                    <p class="text_color text-sm">Start from $2.65</p>
                                                    <div
                                                        class="text-nature-70 text-sm d-flex flex-wrap gap-x-2 align-items-start h-10">
                                                        <p class="title text-[12px] sm:text-sm">
                                                            1 Material
                                                        </p>
                                                        <p class="title text-[12px] sm:text-sm">1 Size</p>
                                                        <p class="title text-[12px] sm:text-sm">
                                                            2 Titles
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="swiper-slide">
                                        <a href="#">
                                            <div class="mockup_prd_wrapper">
                                                <div class="position-relative d-flex justify-content-center mockup_img">
                                                    <img loading="lazy" class="img_prd object-cover"
                                                        src="https://cdn.printway.io/lzi/64f07f1b268e91c78bc75969_800x800.png"
                                                        alt="Transparent Acrylic Car Ornament"
                                                        style="border-radius: 4px 4px 0px 0px" />
                                                </div>
                                                <div class="content_prd_card" style="border-radius: 0px 0px 4px 4px">
                                                    <h3 class="name_prd wrap_two_line_mb">
                                                        Transparent Acrylic Car Ornament
                                                    </h3>
                                                    <p class="text_color text-sm">Start from $2.65</p>
                                                    <div
                                                        class="text-nature-70 text-sm d-flex flex-wrap gap-x-2 align-items-start h-10">
                                                        <p class="title text-[12px] sm:text-sm">
                                                            1 Material
                                                        </p>
                                                        <p class="title text-[12px] sm:text-sm">1 Size</p>
                                                        <p class="title text-[12px] sm:text-sm">
                                                            2 Titles
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
