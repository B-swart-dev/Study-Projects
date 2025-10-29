
import java.util.Scanner;

public class UseBuildingMaterial {

    
    public static void frontMenu(){
       System.out.println("-------FRONT MENU-------");
       System.out.println("(A) Zinc Menu");
       System.out.println("(B) Window Frame Menu");
       System.out.println("(C) Door Menu");
       System.out.println("(Q)uit program");
    
    }//end frontMenu()
    
    public static void main(String[] args) {
       Zinc zinc = new Zinc();
       Door door = new Door();
       WindowFrame windowframe = new WindowFrame();
       
       Scanner in = new Scanner(System.in);
       
       frontMenu();
                   
       System.out.print("Enter option>>>>");
       String option=in.nextLine();
       
       switch(option){
           case "A":
           case "a":    
           zinc.viewZincMenu();
           System.out.print("Enter option>>>>");
           String option2 = in.nextLine();
           zinc.identifierZ = option2;
           switch(option2){
           case "C":
           case "c":    
           zinc.viewCorruZinc();
           System.out.print("Enter option>>>>");
           int option3 = in.nextInt();
           zinc.optionZ = option3;
           switch(option3){
           case 1:
           System.out.print("Enter quantity>>>>");
           int cz1Quantity = in.nextInt();
           zinc.Zq = cz1Quantity; 
           zinc.ZincWork();
           System.out.println(zinc.statement + zinc.totalZ + "\n" 
                   + "Total cost amount is: R" + zinc.totalZ);
           
           System.out.println("---Corrugated Zinc Results----------");
           break;
           case 2:
           System.out.print("Enter quantity>>>>");
           int cz2Quantity = in.nextInt();
           zinc.Zq = cz2Quantity;
           zinc.ZincWork();
           System.out.println(zinc.statement + zinc.totalZ + "\n" 
                   + "Total cost amount is: R" + zinc.totalZ);
           
           System.out.println("---Corrugated Zinc Results----------");
           break;
           case 3:
           System.out.print("Enter quantity>>>>");
           int cz3Quantity = in.nextInt();
           zinc.Zq = cz3Quantity;
           zinc.ZincWork();
           System.out.println(zinc.statement + zinc.totalZ + "\n" 
                   + "Total cost amount is: R" + zinc.totalZ);
																  
           System.out.println("---Corrugated Zinc Results----------");
           break;
           case 4:
           System.out.print("Enter quantity>>>>");
           int cz4Quantity = in.nextInt();
           zinc.Zq = cz4Quantity;
           zinc.ZincWork();
           System.out.println(zinc.statement + zinc.totalZ + "\n" 
                   + "Total cost amount is: R" + zinc.totalZ);
				   
           System.out.println("---Corrugated Zinc Results----------");
           break;
           case 5:
           System.out.print("Enter quantity>>>>");
           int cz5Quantity = in.nextInt();
           zinc.Zq = cz5Quantity;
           zinc.ZincWork();
           System.out.println(zinc.statement + zinc.totalZ + "\n" 
                   + "Total cost amount is: R" + zinc.totalZ);
				   
           System.out.println("---Corrugated Zinc Results----------");
           break;
           case 6:
           System.exit(0);
           break;
           default:
           System.exit(0);
           break;
          }//end nested switch for case "option3"
           break;
           case "I":
           case "i":    
           zinc.viewIBRZinc();
           System.out.print("Enter option>>>>");
           int option4 = in.nextInt();
           zinc.optionZ = option4;
           switch(option4){
           case 1:
           System.out.print("Enter quantity>>>>");
           int ibr1Quantity = in.nextInt();
           zinc.Zq = ibr1Quantity;
           zinc.ZincWork();
           System.out.println(zinc.statement + zinc.totalZ + "\n" 
                   + "Total cost amount is: R" + zinc.totalZ);
          
				   
           System.out.println("---IBR Zinc Results----------");
           break;
           case 2:
           System.out.print("Enter quantity>>>>");
           int ibr2Quantity = in.nextInt();
           zinc.Zq = ibr2Quantity;
           zinc.ZincWork();
           System.out.println(zinc.statement + zinc.totalZ + "\n" 
                   + "Total cost amount is: R" + zinc.totalZ);
				   
           System.out.println("---IBR Zinc Results----------");
           break;
           case 3:
           System.out.print("Enter quantity>>>>");
           int ibr3Quantity = in.nextInt();
           zinc.Zq = ibr3Quantity;
           zinc.ZincWork();
           System.out.println(zinc.statement + zinc.totalZ + "\n" 
                   + "Total cost amount is: R" + zinc.totalZ);
				   
           System.out.println("---IBR Zinc Results----------");
           break;
           case 4:
           System.out.print("Enter quantity>>>>");
           int ibr4Quantity = in.nextInt();
           zinc.Zq = ibr4Quantity;
           zinc.ZincWork();
           System.out.println(zinc.statement + zinc.totalZ + "\n" 
                   + "Total cost amount is: R" + zinc.totalZ);
				   
           System.out.println("---IBR Zinc Results----------");
           break;
           case 5:
           System.out.print("Enter quantity>>>>");
           int ibr5Quantity = in.nextInt();
           zinc.Zq = ibr5Quantity;
           zinc.ZincWork();
           System.out.println(zinc.statement + zinc.totalZ + "\n" 
                   + "Total cost amount is: R" + zinc.totalZ);
				   
           System.out.println("---IBR Zinc Results----------");
           break;
           case 6:
           System.exit(0);
           break;
           default:
           System.exit(0);
           break; 
          }
           break;
           case "E":
           case "e":
           System.exit(0);                        
           break;
           default:
           System.exit(0);
           break;
                         
            }//end nested switch for case "option2"
           break;
               
           case "B":
           case "b":
           windowframe.viewWindowMenu();
           System.out.print("(Enter option>>>>");
           int windowOptions = in.nextInt();
           windowframe.optionW = windowOptions;
           switch(windowOptions){
           case 1:
           System.out.print("Enter quantity>>>>");
           int quantityOfDoubleWindows = in.nextInt();
           windowframe.Wq = quantityOfDoubleWindows;
           windowframe.WindowsWork();
           System.out.println(windowframe.statementW + windowframe.totalW + "\n" 
                   + "Total cost amount is: R" + windowframe.totalW);
           System.out.println("---Results for windowframe----------");
           break;
           case 2:
           System.out.print("Enter quantity>>>>");
           int quantityOfSingleWindows = in.nextInt();
           windowframe.Wq = quantityOfSingleWindows;
           windowframe.WindowsWork();
           System.out.println(windowframe.statementW + windowframe.totalW + "\n" 
                   + "Total cost amount is: R" + windowframe.totalW);
           System.out.println("---Results for windowframe----------");
           break;
           case 3:
           System.exit(0);
           break;
           default:
           System.exit(0);
           break;
           }//end nested switch for case "windowOptions"
           break;
               
           case "C":
           case "c":
           door.viewDoorMenu();
           System.out.print("Enter option>>>>");
           int doorOptions = in.nextInt();
           door.optionD = doorOptions;
           switch(doorOptions){
           case 1:
           System.out.print("Enter quantity>>>>");
           int quantityOfPineStable = in.nextInt();
           door.Dq = quantityOfPineStable;
           door.DoorWork();
           System.out.println(door.statement + door.totalD + "\n" 
                   + "Total cost amount is: R" + door.totalD);
           System.out.println("---Results for door----------");
           break;
           case 2:
           System.out.print("Enter quantity>>>>");
           int quantityOfHardBoard = in.nextInt();
           door.Dq = quantityOfHardBoard;
           door.DoorWork();
           System.out.println(door.statement + door.totalD + "\n" 
                   + "Total cost amount is: R" + door.totalD);     
           System.out.println("---Results for door----------");
           break;
           case 3:
           System.exit(0);
           break;
           default:
           System.exit(0);
           break;
           }//end nested switch for case "doorOptions"
           break;
               
           case "Q":
           case "q":    
           System.exit(0);
           break;
               
           default:
           System.exit(0);
           break;
      }//end main switch for case "option"
           
    }//end static void main
    
} //end UseBuildingMaterial
