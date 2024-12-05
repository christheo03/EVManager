USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[InsertApplication]    Script Date: 12/4/2024 4:17:12 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[InsertApplication]
    @user_id INT,
    @name NVARCHAR(255),
    @carID INT,
    @newAppID INT OUTPUT -- Define an output parameter to return the newly inserted AppID
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        -- Insert a new application into the APPLICATION table
        INSERT INTO APPLICATION (user_id, subsidy_name, carID)
        VALUES (@user_id, @name, @carID);

        -- Retrieve the newly generated AppID
        SET @newAppID = SCOPE_IDENTITY();
    END TRY
    BEGIN CATCH
        -- Handle any errors and rethrow
        DECLARE @ErrorMessage NVARCHAR(4000);
        DECLARE @ErrorSeverity INT;
        DECLARE @ErrorState INT;

        SELECT 
            @ErrorMessage = ERROR_MESSAGE(),
            @ErrorSeverity = ERROR_SEVERITY(),
            @ErrorState = ERROR_STATE();

        RAISERROR (@ErrorMessage, @ErrorSeverity, @ErrorState);
        RETURN;
    END CATCH
END;
GO

