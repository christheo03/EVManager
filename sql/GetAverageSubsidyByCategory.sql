USE [vmarou01]
GO

/****** Object:  StoredProcedure [dbo].[GetAverageSubsidyByCategory]    Script Date: 12/4/2024 4:12:27 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


CREATE   PROCEDURE [dbo].[GetAverageSubsidyByCategory]
AS
BEGIN
    SET NOCOUNT ON;

    -- Calculate average subsidy amount for successful applications grouped by category
    SELECT 
        sub.name AS Category,                      -- The subsidy category (name)
        AVG(sub.amount) AS AverageSubsidyAmount   -- Average subsidy amount
    FROM 
        vmarou01.APPLICATION app                  -- Main application table
    INNER JOIN 
        vmarou01.STATUS stat                      -- Join with STATUS to filter successful applications
    ON 
        app.app_id = stat.app_id
    INNER JOIN 
        vmarou01.SUBSIDY sub                      -- Join with SUBSIDY to fetch subsidy amounts
    ON 
        app.subsidy_name = sub.name               -- Link APPLICATION to SUBSIDY by subsidy_name
    WHERE 
        stat.stage = 'approved'                 -- Only include successful applications
    GROUP BY 
        sub.name                                  -- Group by subsidy category
    ORDER BY 
        AverageSubsidyAmount DESC;                -- Sort by average subsidy in descending order
END;
GO

