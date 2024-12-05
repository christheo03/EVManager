USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[GetApplicationsByUser]    Script Date: 12/4/2024 4:15:47 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[GetApplicationsByUser]
    @user_id INT
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        -- Retrieve applications and their status for the specified user
        SELECT 
            A.app_id,
            A.date_of_app,
            A.user_id,
            A.subsidy_name,
            A.carID,
            S.stage -- Include the stage from the STATUS table
        FROM 
            APPLICATION A
        LEFT JOIN 
            STATUS S ON A.app_id = S.app_id -- Join the STATUS table on app_id
        WHERE 
            A.user_id = @user_id
        ORDER BY 
            A.date_of_app DESC; -- Order by application date, most recent first
    END TRY
    BEGIN CATCH
        -- Handle errors
        DECLARE @ErrorMessage NVARCHAR(4000);
        DECLARE @ErrorSeverity INT;
        DECLARE @ErrorState INT;

        SELECT 
            @ErrorMessage = ERROR_MESSAGE(),
            @ErrorSeverity = ERROR_SEVERITY(),
            @ErrorState = ERROR_STATE();

        RAISERROR (@ErrorMessage, @ErrorSeverity, @ErrorState);
    END CATCH
END;
GO

