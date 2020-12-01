alter table `review_questionare` add is_required boolean DEFAULT 0

alter table `review_questionare` add updated_on timestamp DEFAULT CURRENT_TIMESTAMP

alter table `review_questionare` add updated_by int(11)