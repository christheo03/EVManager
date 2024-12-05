USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[GetOrdersAndDocumentsByAAID]    Script Date: 12/4/2024 4:16:16 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[GetOrdersAndDocumentsByAAID]
    @input_aa_id INT
AS
BEGIN
    SELECT 
        ov.order_id,
        ov.order_date,
        ov.vehicle_details,
        ov.CO2,
        ov.price,
        ov.month_reg,
        ov.year_reg,
        doc.doc_id,
        doc.type,
        doc.size_of_doc,
        doc.up_date,
        doc.path,
        doc.title,
        doc.category
    FROM 
        vmarou01.ORDER_VEHICLE ov
    LEFT JOIN 
        vmarou01.DOCUMENT doc
    ON 
        ov.app_id = doc.app_id
    WHERE 
        ov.aa_id = @input_aa_id
        AND (doc.category = 'order' OR doc.category IS NULL); -- Include only 'order' category or no document
END;
GO

