@extends('frontend.layouts.master')


@section('content')
    <div class="pw_catalog_wrapper">
        <div class="position-sticky top-0 bg-white z-3 pb-3 pt-5">
            <input type="text" placeholder="Search product..." />
            <div class="cursor-pointer position-absolute">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M14.235 16.1046C16.992 14.9329 18.2772 11.7481 17.1055 8.99108C15.9338 6.2341 12.749 4.94896 9.99197 6.12065C7.23498 7.29234 5.94984 10.4772 7.12154 13.2342C8.29323 15.9911 11.4781 17.2763 14.235 16.1046Z"
                        stroke="#5E6C84" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M15.9993 15L19.9993 19" stroke="#7A869A" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                </svg>
            </div>
        </div>

        <div class="breadcrumb_wrapper">
            <div class="d-flex align-items-center mx-auto mt-2 mb-5">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Library
                            </li>
                        </ol>
                    </nav>
                    <h1 class="fw-bold mt-4">All Products</h1>
                </div>
            </div>
        </div>

        <div class="position-relative py-4" style="background-color: #f4f8f9">
            <div class="swiper my-catalog">
                <div class="swiper-wrapper">
                    <div class="swiper-slide text-center">
                        <img src="	https://cdn.printway.io/lzi/666bc61b7ecd3a7dbd7c4624_800x800.jpeg"
                            class="category-img img-fluid d-block mx-auto" alt="Danh mục 1" />
                        <h2 class="category-name">Danh mục 1</h2>
                    </div>
                    <div class="swiper-slide text-center">
                        <img src="	https://cdn.printway.io/lzi/666bf7e8e9f8ecbc0656be49_800x800.jpeg"
                            class="category-img img-fluid d-block mx-auto" alt="Danh mục 2" />
                        <h2 class="category-name">Danh mục 2</h2>
                    </div>
                    <div class="swiper-slide text-center">
                        <img src="	https://cdn.printway.io/lzi/666c00e33dea86065a84db26_800x800.jpeg"
                            class="category-img img-fluid d-block mx-auto" alt="Danh mục 3" />
                        <h2 class="category-name">Danh mục 3</h2>
                    </div>
                    <div class="swiper-slide text-center">
                        <img src="https://cdn.printway.io/lzi/666c0e896f96e33ccef7c815_800x800.jpeg"
                            class="category-img img-fluid d-block mx-auto" alt="Danh mục 4" />
                        <h2 class="category-name">Danh mục 4</h2>
                    </div>
                    <div class="swiper-slide text-center">
                        <img src="https://cdn.printway.io/lzi/666c1b083dea86065a84db98_800x800.jpeg"
                            class="category-img img-fluid d-block mx-auto" alt="Danh mục 5" />
                        <h2 class="category-name">Danh mục 5</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex w-100 d-md-none">
            <button id="showFilterBtn" type="button"
                class="btn btn-outline-secondary d-flex align-items-center justify-content-center w-100">
                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M8.33301 6.25C8.33301 6.66136 7.99436 7 7.58301 7C7.17165 7 6.83301 6.66136 6.83301 6.25C6.83301 5.83864 7.17165 5.5 7.58301 5.5C7.99436 5.5 8.33301 5.83864 8.33301 6.25ZM6.83301 10C6.83301 9.58864 7.17165 9.25 7.58301 9.25C7.99436 9.25 8.33301 9.58864 8.33301 10C8.33301 10.4114 7.99436 10.75 7.58301 10.75C7.17165 10.75 6.83301 10.4114 6.83301 10ZM6.83301 13.75C6.83301 13.3386 7.17165 13 7.58301 13C7.99436 13 8.33301 13.3386 8.33301 13.75C8.33301 14.1614 7.99436 14.5 7.58301 14.5C7.17165 14.5 6.83301 14.1614 6.83301 13.75Z"
                        fill="#7A869A" stroke="#5E6C84"></path>
                    <path
                        d="M13.416 7.5C14.1035 7.5 14.666 6.9375 14.666 6.25C14.666 5.5625 14.1035 5 13.416 5C12.7285 5 12.166 5.5625 12.166 6.25C12.166 6.9375 12.7285 7.5 13.416 7.5ZM13.416 8.75C12.7285 8.75 12.166 9.3125 12.166 10C12.166 10.6875 12.7285 11.25 13.416 11.25C14.1035 11.25 14.666 10.6875 14.666 10C14.666 9.3125 14.1035 8.75 13.416 8.75ZM13.416 12.5C12.7285 12.5 12.166 13.0625 12.166 13.75C12.166 14.4375 12.7285 15 13.416 15C14.1035 15 14.666 14.4375 14.666 13.75C14.666 13.0625 14.1035 12.5 13.416 12.5Z"
                        fill="#7A869A"></path>
                </svg>
                <span class="ms-2">Show Filter</span>
            </button>
        </div>

        <div class="list-products d-flex mb-4 pt-3">
            <div id="filter-form">
                <form action="">
                    <div class="mt-2 mb-4">
                        <div class="d-md-flex flex-column position-relative" style="min-width: 235px">
                            <h4 class="fw-bold text-base">Fulfillment Location</h4>
                            <div class="pb-4">
                                <ul class="list-unstyled">
                                    <li class="form-check">
                                        <input class="form-check-input" type="checkbox" value="EU" id="locationEU" />
                                        <label class="form-check-label" for="locationEU">European</label>
                                    </li>
                                    <li class="form-check">
                                        <input class="form-check-input" type="checkbox" value="AU" id="locationAU" />
                                        <label class="form-check-label" for="locationAU">Australia</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="w-100">
                <p class="mb-4 mt-2 text-sm" style="color: #97a0af">
                    Showing 1-20 / 359 products
                </p>
                <div class="list_prd_catalogs w-100">
                    <div class="box_product_item border">
                        <a href="/en/product/table-mat-tbmt">
                            <div class="mockup_prd_wrapper rounded">
                                <img loading="lazy" class="img-fluid w-100 rounded-top"
                                    src="https://cdn.printway.io/lzi/67dbd70fdb2568433ce9e039_800x800.png" alt="Table Mat"
                                    style="border-radius: 4px 4px 0px 0px" />
                            </div>
                            <div class="content_prd_card p-3 rounded-bottom" style="border-radius: 0px 0px 4px 4px">
                                <h3 class="name_prd text-truncate">Table Mat</h3>
                                <p class="text-muted text-sm mb-2">Start from $4.24</p>
                                <div class="d-flex flex-wrap gap-2 align-items-start h-10">
                                    <p class="title text-muted small">2 Sizes</p>
                                    <p class="title text-muted small">2 Sizes</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="box_product_item border">
                        <a href="/en/product/table-mat-tbmt">
                            <div class="mockup_prd_wrapper rounded">
                                <img loading="lazy" class="img-fluid w-100 rounded-top"
                                    src="https://cdn.printway.io/lzi/67dbd70fdb2568433ce9e039_800x800.png" alt="Table Mat"
                                    style="border-radius: 4px 4px 0px 0px" />
                            </div>
                            <div class="content_prd_card p-3 rounded-bottom" style="border-radius: 0px 0px 4px 4px">
                                <h3 class="name_prd text-truncate">Table Mat</h3>
                                <p class="text-muted text-sm mb-2">Start from $4.24</p>
                                <div class="d-flex flex-wrap gap-2 align-items-start h-10">
                                    <p class="title text-muted small">2 Sizes</p>
                                    <p class="title text-muted small">2 Sizes</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="box_product_item border">
                        <a href="/en/product/table-mat-tbmt">
                            <div class="mockup_prd_wrapper rounded">
                                <img loading="lazy" class="img-fluid w-100 rounded-top"
                                    src="https://cdn.printway.io/lzi/67dbd70fdb2568433ce9e039_800x800.png" alt="Table Mat"
                                    style="border-radius: 4px 4px 0px 0px" />
                            </div>
                            <div class="content_prd_card p-3 rounded-bottom" style="border-radius: 0px 0px 4px 4px">
                                <h3 class="name_prd text-truncate">Table Mat</h3>
                                <p class="text-muted text-sm mb-2">Start from $4.24</p>
                                <div class="d-flex flex-wrap gap-2 align-items-start h-10">
                                    <p class="title text-muted small">2 Sizes</p>
                                    <p class="title text-muted small">2 Sizes</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="box_product_item border">
                        <a href="/en/product/table-mat-tbmt">
                            <div class="mockup_prd_wrapper rounded">
                                <img loading="lazy" class="img-fluid w-100 rounded-top"
                                    src="https://cdn.printway.io/lzi/67dbd70fdb2568433ce9e039_800x800.png" alt="Table Mat"
                                    style="border-radius: 4px 4px 0px 0px" />
                            </div>
                            <div class="content_prd_card p-3 rounded-bottom" style="border-radius: 0px 0px 4px 4px">
                                <h3 class="name_prd text-truncate">Table Mat</h3>
                                <p class="text-muted text-sm mb-2">Start from $4.24</p>
                                <div class="d-flex flex-wrap gap-2 align-items-start h-10">
                                    <p class="title text-muted small">2 Sizes</p>
                                    <p class="title text-muted small">2 Sizes</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="box_product_item border">
                        <a href="/en/product/table-mat-tbmt">
                            <div class="mockup_prd_wrapper rounded">
                                <img loading="lazy" class="img-fluid w-100 rounded-top"
                                    src="https://cdn.printway.io/lzi/67dbd70fdb2568433ce9e039_800x800.png" alt="Table Mat"
                                    style="border-radius: 4px 4px 0px 0px" />
                            </div>
                            <div class="content_prd_card p-3 rounded-bottom" style="border-radius: 0px 0px 4px 4px">
                                <h3 class="name_prd text-truncate">Table Mat</h3>
                                <p class="text-muted text-sm mb-2">Start from $4.24</p>
                                <div class="d-flex flex-wrap gap-2 align-items-start h-10">
                                    <p class="title text-muted small">2 Sizes</p>
                                    <p class="title text-muted small">2 Sizes</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="pagination">
                    <div class="d-flex justify-content-center align-items-center">
                        <ul class="custom-pagination">
                            <li class="page-item disabled">
                                <i class="bi bi-chevron-left"></i>
                            </li>
                            <li class="page-item active">1</li>
                            <li class="page-item">2</li>
                            <li class="page-item">3</li>
                            <li class="page-item">4</li>
                            <li class="page-item">5</li>
                            <li class="page-item dots">...</li>
                            <li class="page-item">18</li>
                            <li class="page-item"><i class="bi bi-chevron-right"></i></li>
                        </ul>

                        <!-- Dropdown chọn số lượng per page -->
                        <select class="per-page-selector ms-3">
                            <option value="10">10 / page</option>
                            <option value="20" selected>20 / page</option>
                            <option value="50">50 / page</option>
                            <option value="100">100 / page</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-popup" id="filterPopup">
        <div class="d-flex justify-content-between align-items-center border-bottom px-3">
            <h3 class="mb-0 fs-6 fw-bold">Bộ lọc</h3>
            <span class="close-btn" id="closeFilterBtn">&times;</span>
        </div>
        <div class="px-3" id="show-filter-form"></div>
    </div>
@endsection
