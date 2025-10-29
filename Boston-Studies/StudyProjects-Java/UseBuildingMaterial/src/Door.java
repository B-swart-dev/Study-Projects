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
public class Door extends BuildingMaterial{
 
    private Scanner in;
    public double totalD;
    public int optionD, Dq, money;
    public String typeD;
    String statement;
    public Door() {
        in = new Scanner(System.in);
    }
    @Override
    public void totalCostPrice() {
        totalD = getCostPrice() * getQUANTITY();
    }
    public void viewDoorMenu() {
        System.out.println("-------DOOR MENU-------");
        System.out.println("(1) Pine Stable Door @ R330 each");
        System.out.println("(2) Hard Board Door @ R270 each");
        System.out.println("(3) Exit");
    } 
    public void variables() {
         setQUANTITY(Dq);
         setCostPrice(money);         
         statement = (Dq + "x " + typeD + " @ R");
    }
    public void DoorWork() {
                switch(optionD){
                    case 1:
                        typeD = "Pine Stable Door";
                        money = 300;
                        variables();
                        totalCostPrice();
                        break;
                    case 2:
                        typeD = "Hard Board Door";
                        money = 270;
                        variables();
                        totalCostPrice();
                        break;                   
                }
    }
}
