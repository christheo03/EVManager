USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[GetActiveOrSentApplications]    Script Date: 12/4/2024 4:14:27 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[GetActiveOrSentApplications]
AS
BEGIN
    SET NOCOUNT ON; -- Turns off the message that shows the number of rows affected

    SELECT 
        A.app_id,
        A.date_of_app,
        A.user_id,
        A.subsidy_name,
        A.carID,
        S.status_id,
        S.stage,
        S.date_of_modify,
        S.reason_of_modify,
        D.doc_id,
        D.type,
        D.size_of_doc,
        D.up_date,
        D.path,
        D.title,
        D.category
    FROM 
        vmarou01.APPLICATION A
        INNER JOIN vmarou01.STATUS S ON A.app_id = S.app_id
        INNER JOIN vmarou01.DOCUMENT D ON A.app_id = D.app_id
    WHERE 
        S.stage IN ('order sent', 'active')
    ORDER BY 
        A.app_id;
END;

GO

