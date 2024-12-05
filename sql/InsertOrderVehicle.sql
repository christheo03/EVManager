USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[InsertOrderVehicle]    Script Date: 12/4/2024 4:17:38 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[InsertOrderVehicle]
    @vehicle_details NVARCHAR(255),
    @CO2 INT = NULL,
    @month_reg TINYINT,
    @year_reg SMALLINT,
    @price INT,
    @app_id INT,
    @aa_id INT,
    @new_orderID INT OUTPUT
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        INSERT INTO vmarou01.ORDER_VEHICLE
            (vehicle_details, CO2, price, app_id, aa_id, month_reg, year_reg)
        VALUES
            (@vehicle_details, @CO2, @price, @app_id, @aa_id, @month_reg, @year_reg);

        -- Retrieve the newly generated AppID
        SET @new_orderID = SCOPE_IDENTITY();
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

