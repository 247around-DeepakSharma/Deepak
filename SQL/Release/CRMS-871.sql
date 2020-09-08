
CREATE TABLE `msl_consumed_ow` (
  `id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `inventory_id` int NOT NULL,
  `quantity` int NOT NULL,
  `agent_id` int NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `msl_consumed_ow`
--
ALTER TABLE `msl_consumed_ow`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `msl_consumed_ow`
--
ALTER TABLE `msl_consumed_ow`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

RENAME TABLE `247around`.`msl_consumed_ow` TO `247around`.`non_returnable_consumed_parts`;

