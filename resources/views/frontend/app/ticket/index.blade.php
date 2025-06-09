@extends('frontend.app')

@section('content')
    <div class="order">
        <div class="order_container">
            <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
                <h1 class="billing__title__content">Tickets</h1>
                <button class="ant-btn ant-btn-default px-3 text-f06022 border-f06022" data-bs-toggle="modal"
                    data-bs-target="#createTicketModal">
                    Create ticket <i class="bi bi-plus-circle-dotted ms-2"></i>
                </button>

            </div>
        </div>

        <form id="order-filter-form" class="row flex-wrap mt-4">
            <!-- Ô tìm kiếm -->
            <div class="form-group position-relative col-5">
                <label class="form-label fw-bold">Search</label>
                <div class="form-group input-icon-right">
                    <input type="search" class="form-control" name="search" placeholder="Search by Ticket ID, Order ID">
                    <i class="bi bi-search"></i>
                </div>
            </div>

            <div class="form-group col-2">
                <label class="form-label fw-bold">Date</label>
                <input type="text" id="date-range" name="date_range" class="form-control"
                    placeholder="Select date range" />
            </div>

            <div class="form-group col-2">
                <label class="form-label fw-bold">Subject</label>
                <select name="subject" id="subject" class="form-select">
                    <option value="">--- Subject ---</option>
                    @foreach ($subjects as $id => $title)
                        <option value="{{ $id }}">{{ $title }}</option>
                    @endforeach
                </select>

            </div>
        </form>

        <div class="d-flex gap-2 flex-wrap mt-4">
            <button class="filter-btn active" data-status="all">
                All ({{ $totalCount }})
            </button>
            <button class="filter-btn" data-status="open">
                Open ({{ $statusCounts['open'] ?? 0 }})
            </button>
            <button class="filter-btn" data-status="resolving">
                Resolving ({{ $statusCounts['resolving'] ?? 0 }})
            </button>
            <button class="filter-btn" data-status="resolved">
                Resolved ({{ $statusCounts['resolved'] ?? 0 }})
            </button>
            <button class="filter-btn" data-status="closed">
                Closed ({{ $statusCounts['closed'] ?? 0 }})
            </button>
        </div>


        <div class="table-responsive mt-4">
            <div id="ticket-content">
            </div>
        </div>
    </div>

    @include('frontend/components/create-ticket-modal')

    <div class="modal fade" id="closeTicketModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-close-ticket">
                @csrf
                <input type="hidden" name="ticket_id" id="close-ticket-id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Close ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="close_reason" class="form-label fw-bold">Reason <span
                                    class="text-danger">*</span></label>
                            <textarea name="reason" id="close_reason" class="form-control" rows="4" placeholder="Reason" required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="ant-btn ant-btn-default px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="ant-btn ant-btn-primary px-3">Confirm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="ratingModalLabel">Service review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">

                    <div class="text-center mb-3">
                        <div id="ratingText" class="mb-2 fw-bold text-secondary">Chọn đánh giá</div>
                        <div id="stars" class="d-flex justify-content-center">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="star fa fa-star fs-3 mx-1" data-value="{{ $i }}"></i>
                            @endfor
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ratingDescription" class="form-lable fw-bold">Mô tả</label>
                        <textarea class="form-control" id="ratingDescription" rows="3" placeholder="Viết mô tả..."></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="ant-btn ant-btn-default px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="ant-btn ant-btn-primary px-3" id="submitRating">Submit a
                        review</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/js/select2.min.js') }}"></script>

    <script>
        let selectedRating = 0;
        const ratingText = {
            1: 'Poor',
            2: 'Unsatisfactory',
            3: 'Normal',
            4: 'Good',
            5: 'Very good'
        };

        $(document).ready(function() {
            $('#ratingModal').on('show.bs.modal', function() {
                selectedRating = 0;
                highlightStars(0);
                $('#ratingText').text('Chọn đánh giá');
                $('#ratingDescription').val('');
            });

            $('.star').on('mouseenter', function() {
                const value = $(this).data('value');
                highlightStars(value);
                $('#ratingText').text(ratingText[value]);
            });

            $('.star').on('mouseleave', function() {
                highlightStars(selectedRating);
                $('#ratingText').text(selectedRating ? ratingText[selectedRating] : 'Select review');
            });

            $('.star').on('click', function() {
                selectedRating = $(this).data('value');
                highlightStars(selectedRating);
                $('#ratingText').text(ratingText[selectedRating]);
            });

            $('#submitRating').on('click', function() {
                const description = $('#ratingDescription').val();
                const ticketId = $('[data-ticket-id]').data('ticket-id');

                $.ajax({
                    url: '/tickets/rate',
                    method: 'POST',
                    data: {
                        rating: selectedRating,
                        description: description,
                        ticket_id: ticketId
                    },
                    beforeSend: function() {
                        $('#loadingOverlay').show();
                    },
                    success: function(response) {
                        $('#ratingModal').modal('hide');
                        datgin.success(response.message)

                        if (response.data && response.data.statusCounts) {
                            updateTotalStatus(response)
                        }

                        fetchTicket();

                        lastOpenedTicketId = null
                    },
                    error: function(xhr) {
                        datgin.error('Lỗi:', xhr.responseJSON?.message || 'Đã xảy ra lỗi')
                    },
                    complete: function() {
                        $('#loadingOverlay').hide();
                    }
                });
            });


            function highlightStars(rating) {
                $('.star').each(function() {
                    const value = $(this).data('value');
                    $(this).toggleClass('hover', value <= rating);
                    $(this).toggleClass('selected', value <= rating);
                });
            }
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/icomoon/style.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/select2.min.css') }}">

    <style>
        figure.image {
            justify-content: center;
            display: flex
        }

        .message-content img {
            max-width: 300px;
            height: auto;
            border-radius: 8px;
            /* nếu muốn bo tròn ảnh */
            display: block;
        }

        .star {
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star.hover,
        .star.selected {
            color: #f4c150;
        }
    </style>
@endpush
