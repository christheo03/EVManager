USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[GetApplicationDetails]    Script Date: 12/4/2024 4:14:54 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[GetApplicationDetails]
AS
BEGIN
    SET NOCOUNT ON;

    -- Common Table Expression (CTE) to find the latest status for each application
    WITH LatestStatus AS (
        SELECT 
            app_id,
            MAX(date_of_modify) AS LastModified
        FROM vmarou01.STATUS
        GROUP BY app_id
    )
    SELECT 
        A.app_id,
        A.date_of_app,
        A.user_id,
        A.subsidy_name,
        A.carID,
        O.order_id,
        O.order_date,
        O.vehicle_details,
        O.CO2,
        O.price,
        O.month_reg,
        O.year_reg,
        D.doc_id,
        D.type,
        D.size_of_doc,
        D.up_date,
        D.path,
        D.title,
        D.category,
        S.status_id,
        S.stage,
        S.date_of_modify AS status_date_of_modify,
        S.reason_of_modify
    FROM vmarou01.APPLICATION A
    LEFT JOIN vmarou01.ORDER_VEHICLE O ON A.app_id = O.app_id
    LEFT JOIN vmarou01.DOCUMENT D ON A.app_id = D.app_id
    LEFT JOIN vmarou01.STATUS S 
        ON A.app_id = S.app_id 
        AND S.date_of_modify = (
            SELECT MAX(date_of_modify) 
            FROM vmarou01.STATUS 
            WHERE vmarou01.STATUS.app_id = A.app_id
        ) -- Ensure the latest status is included
    ORDER BY A.app_id;

END;
GO

