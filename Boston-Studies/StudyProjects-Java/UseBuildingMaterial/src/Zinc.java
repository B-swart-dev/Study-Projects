/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 import java.util.Scanner;
/**
 *
 * @author lastp
 */

public class Zinc extends BuildingMaterial {
//...
    private Scanner in;
    public String identifierZ, ZincType = "";
    public int optionZ, Zq, money;
    public double MeasurementZ;
    public double totalZ;
    
    String statement;
    public Zinc() {
        in = new Scanner(System.in);
    }
    @Override
    public void totalCostPrice() {
        totalZ = getCostPrice() * getQUANTITY();
    }
    public void viewZincMenu() {
        System.out.println("-------ZINC MENU-------");
        System.out.println("(C) Corrugated Zinc");
        System.out.println("(I) IBR Zinc");
    }
    
    public void viewCorruZinc() {
        System.out.println("-------CORRUGATED ZINC-------");
        System.out.println("(1) 2.2mm @ R77 each");
        System.out.println("(2) 2.4m @ R85 each");
        System.out.println("(3) 2.7m @ R95 each");
        System.out.println("(4) 3m @ R105 each");
        System.out.println("(5) 3.6m @ R123 each");
        System.out.println("(6) Exit");
    }
    
    public void viewIBRZinc() {
        System.out.println("-------IBR ZINC-------");
        System.out.println("(1) 3m @ R123 each");
        System.out.println("(2) 3.3m @ R133 each");
        System.out.println("(3) 3.6m @ R143 each");
        System.out.println("(4) 4m @ R160 each");
        System.out.println("(5) 4.2m @ R167 each");
        System.out.println("(6) Exit");
    }
     public void variables() {
         setQUANTITY(Zq);
         setCostPrice(money);
         if (identifierZ.equals("C")) {
            ZincType = "Corrugated Zinc";  
                                      } 
         if (identifierZ.equals("I")) {
            ZincType = "IBR Zinc";  
                                      }         
         statement = (Zq + "x " + MeasurementZ + "m " + ZincType + " @ R");
    }
    public void ZincWork() {
        switch(identifierZ){
            case "C":
            case "c":
                switch(optionZ){
                    case 1:
                        MeasurementZ = 2.2;
                        money = 77;
                        variables();
                        totalCostPrice();
                        break;
                    case 2:
                        MeasurementZ = 2.4;
                        money = 85;
                        variables();
                        totalCostPrice();
                        break;
                    case 3:
                        MeasurementZ = 2.7;
                        money = 95;
                        variables();
                        totalCostPrice();
                        break;
                    case 4:
                        MeasurementZ = 3;
                        money = 105;
                        variables();
                        totalCostPrice();
                        break;
                    case 5:
                        MeasurementZ = 3.6;
                        money = 123;
                        variables();
                        totalCostPrice();
                        break;
                } 
                break;
            case "I":
            case "i":    
                switch(optionZ){
                    case 1:
                        MeasurementZ = 3;
                        money = 123;
                        variables();
                        totalCostPrice();
                        break;
                    case 2:
                        MeasurementZ = 3.3;
                        money = 133;
                        variables();
                        totalCostPrice();
                        break;
                    case 3:
                        MeasurementZ = 3.6;
                        money = 143;
                        variables();
                        totalCostPrice();
                        break;
                    case 4:
                        MeasurementZ = 4;
                        money = 160;
                        variables();
                        totalCostPrice();
                        break;
                    case 5:
                        MeasurementZ = 4.2;
                        money = 167;
                        variables();
                        totalCostPrice();
                        break;
                                }
                break;
            
    }

    
}
    }
